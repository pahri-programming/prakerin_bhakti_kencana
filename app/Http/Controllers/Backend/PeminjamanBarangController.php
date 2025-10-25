<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PeminjamanBarang;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeminjamanBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //  INDEX: auto update status & stok kalau sudah lewat waktu
    public function index(Request $request)
    {
        // Auto-update status ke "selesai" kalau lewat waktu
        $expired = PeminjamanBarang::where(function ($q) {
            $q->where('tanggal', '<', now()->toDateString())
                ->orWhere(function ($s) {
                    $s->where('tanggal', now()->toDateString())
                        ->where('waktu_selesai', '<', now()->format('H:i:s'));
                });
        })
            ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam'])
            ->get();

        foreach ($expired as $p) {
            $p->status = 'selesai';
            $p->save();

            // kembalikan stok barang
            $barang = $p->barang;
            $barang->stok += $p->jumlah;
            $barang->save();
        }

        $query = PeminjamanBarang::with(['user', 'barang'])->orderByDesc('tanggal');

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->get()->map(function ($p) {
            $p->tanggal_format = Carbon::parse($p->tanggal)->translatedFormat('d F Y');
            return $p;
        });

        $barangs = Barang::all();
        confirmDelete('Data Peminjaman', 'Apakah anda yakin ingin menghapus data ini?');
        return view('backend.peminjaman.index', compact('peminjaman', 'barangs'));
    }

    //  CREATE
    public function create()
    {
        $barangs = Barang::all();
        $users   = User::all();
        return view('backend.peminjaman.create', compact('barangs', 'users'));
    }

    //  STORE
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'barang_id'     => 'required|exists:barangs,id',
            'jumlah'        => 'required|integer|min:1',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'keterangan'    => 'nullable|string',
        ]);

        $barang = Barang::find($request->barang_id);
        if (! $barang) {
            toast('Barang tidak ditemukan.', 'error');
            return back();
        }

        // cek stok & waktu overlap
        $cek = $this->checkAvailability(
            $barang->id,
            $request->tanggal,
            $request->waktu_mulai,
            $request->waktu_selesai,
            (int) $request->jumlah
        );

        if (! $cek['ok']) {
            toast($cek['message'], 'error');
            return back()->withInput();
        }

        // simpan manual (biar gak keliatan auto)
        $peminjaman                = new PeminjamanBarang();
        $peminjaman->user_id       = $request->user_id;
        $peminjaman->barang_id     = $barang->id;
        $peminjaman->jumlah        = (int) $request->jumlah;
        $peminjaman->tanggal       = $request->tanggal;
        $peminjaman->waktu_mulai   = $request->waktu_mulai;
        $peminjaman->waktu_selesai = $request->waktu_selesai;
        $peminjaman->keterangan    = $request->keterangan ?? '-';
        $peminjaman->status        = 'menunggu'; // default status awal
        $peminjaman->save();

        // opsional: catat log
        \Log::info('Peminjaman baru dibuat', ['id' => $peminjaman->id, 'barang' => $barang->nama]);

        toast('Peminjaman berhasil diajukan, menunggu persetujuan.', 'success');
        return redirect()->route('backend.peminjaman.index');
    }

    //  EDIT
    public function edit($id)
    {
        $peminjaman = PeminjamanBarang::findOrFail($id);
        $barangs    = Barang::all();
        $users      = User::all();
        return view('backend.peminjaman.edit', compact('peminjaman', 'barangs', 'users'));
    }

    // UPDATE (stok otomatis)
    public function update(Request $request, $id)
    {
        $peminjaman = PeminjamanBarang::findOrFail($id);

        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'barang_id'     => 'required|exists:barangs,id',
            'jumlah'        => 'required|integer|min:1',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'status'        => 'required|in:menunggu,disetujui,ditolak,dipinjam,dikembalikan,selesai',
            'keterangan'    => 'nullable|string',
        ]);

        $barang = Barang::find($request->barang_id);
        if (! $barang) {
            toast('Barang tidak ditemukan.', 'error');
            return back();
        }

        $oldStatus = $peminjaman->status;
        $newStatus = $request->status;

        // kalau dari status belum dipinjam → jadi dipinjam, kurangi stok
        if (in_array($newStatus, ['disetujui', 'dipinjam']) && ! in_array($oldStatus, ['disetujui', 'dipinjam'])) {
            if ($barang->stok < $request->jumlah) {
                toast("Stok {$barang->nama} tidak mencukupi.", 'error');
                return back()->withInput();
            }
            $barang->stok -= $request->jumlah;
            $barang->save();
        }

        // kalau dari dipinjam → dikembalikan/selesai, tambahkan stok lagi
        if (in_array($newStatus, ['selesai', 'dikembalikan']) && in_array($oldStatus, ['dipinjam', 'disetujui'])) {
            $barang->stok += $peminjaman->jumlah;
            $barang->save();
        }

        // update manual field by field
        $peminjaman->user_id       = $request->user_id;
        $peminjaman->barang_id     = $request->barang_id;
        $peminjaman->jumlah        = (int) $request->jumlah;
        $peminjaman->tanggal       = $request->tanggal;
        $peminjaman->waktu_mulai   = $request->waktu_mulai;
        $peminjaman->waktu_selesai = $request->waktu_selesai;
        $peminjaman->status        = $request->status;
        $peminjaman->keterangan    = $request->keterangan ?? '-';
        $peminjaman->save();

        \Log::info('Peminjaman diupdate', [
            'id'          => $peminjaman->id,
            'status_lama' => $oldStatus,
            'status_baru' => $newStatus,
        ]);

        toast('Data peminjaman berhasil diperbarui.', 'success');
        return redirect()->route('backend.peminjaman.index');
    }

    public function destroy($id)
    {
        $p = PeminjamanBarang::findOrFail($id);
        $p->delete();
        toast('Peminjaman dihapus', 'success');
        return back();
    }

    //  EXPORT PDF
    public function export()
    {
        $data = PeminjamanBarang::with(['user', 'barang'])->latest()->get();
        $pdf  = Pdf::loadView('backend.peminjaman.pdf', compact('data'))->setPaper('A4', 'landscape');
        return $pdf->download('laporan-peminjaman.pdf');
    }

    // HELPER: cek ketersediaan stok & waktu
    protected function checkAvailability($barangId, $tanggal, $mulai, $selesai, $jumlah, $excludeId = null)
    {
        $barang = Barang::find($barangId);
        if (! $barang) {
            return ['ok' => false, 'message' => 'Barang tidak ditemukan.'];
        }

        $query = PeminjamanBarang::where('barang_id', $barangId)
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam'])
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('waktu_mulai', [$mulai, $selesai])
                    ->orWhereBetween('waktu_selesai', [$mulai, $selesai])
                    ->orWhere(function ($r) use ($mulai, $selesai) {
                        $r->where('waktu_mulai', '<=', $mulai)
                            ->where('waktu_selesai', '>=', $selesai);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $totalReserved = $query->sum('jumlah');
        $available     = $barang->stok - $totalReserved;

        if ($available <= 0) {
            return ['ok' => false, 'message' => "Stok {$barang->nama} habis pada waktu tersebut."];
        }

        if ($jumlah > $available) {
            return ['ok' => false, 'message' => "Hanya tersedia {$available} unit {$barang->nama}."];
        }

        return ['ok' => true, 'message' => 'Tersedia'];
    }
}
