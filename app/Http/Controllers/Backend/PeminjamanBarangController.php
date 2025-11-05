<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PeminjamanBarang;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PeminjamanBarangController extends Controller
{
    public function export()
    {
        $data = PeminjamanBarang::with(['user', 'barang'])
            ->latest()
            ->get()
            ->map(function ($p) {
                $p->tanggal_format = Carbon::parse($p->tanggal)->translatedFormat('d F Y');
                return $p;
            });

        $pdf = Pdf::loadView('backend.peminjaman.pinjampdf', compact('data'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * INDEX
     */
    public function index(Request $request)
    {
        // ✅ Auto-update status "selesai" kalau sudah lewat waktu
        $expired = PeminjamanBarang::whereNotIn('status', ['selesai', 'dikembalikan'])
            ->where(function ($q) {
                $q->where('tanggal', '<', now()->toDateString())
                    ->orWhere(function ($s) {
                        $s->where('tanggal', now()->toDateString())
                            ->where('waktu_selesai', '<', now()->format('H:i:s'));
                    });
            })
            ->get();

        foreach ($expired as $p) {
            $p->status = 'selesai';
            $p->save();

            // kembalikan stok
            if ($p->barang) {
                $p->barang->increment('stok', $p->jumlah);
            }
        }

        // ✅ Query utama
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

    /**
     * CREATE
     */
    public function create()
    {
        $barangs = Barang::all();
        $users   = User::all();
        return view('backend.peminjaman.create', compact('barangs', 'users'));
    }

    /**
     * STORE: buat peminjaman baru + validasi stok & waktu
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'barang_id'     => 'required|exists:barangs,id',
            'jumlah'        => 'required|integer|min:1',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'keterangan'    => 'required|string|min:3',
        ], [
            'required'    => ':attribute harus diisi',
            'exists'      => ':attribute tidak valid',
            'integer'     => ':attribute harus berupa angka',
            'min'         => ':attribute minimal :min',
            'date'        => ':attribute harus berupa tanggal',
            'date_format' => ':attribute harus berupa format waktu yang valid',
            'after'       => ':attribute harus setelah waktu mulai',
        ]);

        $startDateTime = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
        if ($startDateTime->lt(Carbon::now())) {
            toast('Waktu mulai sudah lewat. Silakan pilih waktu yang valid.', 'error');
            return back()->withInput();
        }

        $barang = Barang::findOrFail($request->barang_id);

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

        try {
            $peminjaman = PeminjamanBarang::create([
                'user_id'       => $validated['user_id'],
                'barang_id'     => $barang->id,
                'jumlah'        => (int) $validated['jumlah'],
                'tanggal'       => $validated['tanggal'],
                'waktu_mulai'   => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'keterangan'    => $validated['keterangan'],
                'status'        => 'menunggu',
            ]);

            Log::info('Peminjaman baru dibuat', [
                'id'     => $peminjaman->id,
                'barang' => $barang->nama,
            ]);

            toast('Peminjaman berhasil diajukan, menunggu persetujuan.', 'success');
            return redirect()->route('backend.peminjaman.index');

        } catch (\Exception $e) {
            Log::error('Gagal membuat peminjaman: ' . $e->getMessage());
            toast('Gagal membuat peminjaman. Silakan coba lagi.', 'error');
            return back()->withInput();
        }
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'user_id'       => 'required|exists:users,id',
    //         'barang_id'     => 'required|exists:barangs,id',
    //         'jumlah'        => 'required|integer|min:1',
    //         'tanggal'       => 'required|date',
    //         'waktu_mulai'   => 'required',
    //         'waktu_selesai' => 'required|after:waktu_mulai',
    //         'keterangan'    => 'nullable|string',
    //     ]);

    //     // cek jam sudah lewat (gabungkan tanggal + waktu)
    //     $startDateTime = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
    //     if ($startDateTime->lt(Carbon::now())) {
    //         toast('Waktu mulai sudah lewat. Silakan pilih waktu yang valid.', 'error');
    //         return back()->withInput();
    //     }

    //     $barang = Barang::find($request->barang_id);
    //     if (! $barang) {
    //         toast('Barang tidak ditemukan.', 'error');
    //         return back()->withInput();
    //     }

    //     // cek stok & bentrok jadwal
    //     $cek = $this->checkAvailability(
    //         $barang->id,
    //         $request->tanggal,
    //         $request->waktu_mulai,
    //         $request->waktu_selesai,
    //         (int) $request->jumlah
    //     );

    //     if (! $cek['ok']) {
    //         toast($cek['message'], 'error');
    //         return back()->withInput();
    //     }

    //     $peminjaman = PeminjamanBarang::create([
    //         'user_id'       => $request->user_id,
    //         'barang_id'     => $barang->id,
    //         'jumlah'        => (int) $request->jumlah,
    //         'tanggal'       => $request->tanggal,
    //         'waktu_mulai'   => $request->waktu_mulai,
    //         'waktu_selesai' => $request->waktu_selesai,
    //         'keterangan'    => $request->keterangan ?? '-',
    //         'status'        => 'menunggu',
    //     ]);

    //     Log::info('Peminjaman baru dibuat', [
    //         'id'     => $peminjaman->id,
    //         'barang' => $barang->nama,
    //     ]);

    //     toast('Peminjaman berhasil diajukan, menunggu persetujuan.', 'success');
    //     return redirect()->route('backend.peminjaman.index');
    // }

    public function show($id)
    {
        $peminjaman = PeminjamanBarang::with(['user', 'barang'])->findOrFail($id);

        // format tanggal & waktu untuk tampilan
        $peminjaman->tanggal_format = Carbon::parse($peminjaman->tanggal)->translatedFormat('d F Y');
        $peminjaman->waktu_range    = "{$peminjaman->waktu_mulai} - {$peminjaman->waktu_selesai}";

        return view('backend.peminjaman.show', compact('peminjaman'));
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        $peminjaman = PeminjamanBarang::findOrFail($id);
        $barangs    = Barang::all();
        $users      = User::all();
        return view('backend.peminjaman.edit', compact('peminjaman', 'barangs', 'users'));
    }

    /**
     * UPDATE: ubah data + sesuaikan stok sesuai perubahan status
     */
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

        //Update stok hanya kalau status berubah
        if ($oldStatus !== $newStatus) {
            if (in_array($newStatus, ['disetujui', 'dipinjam']) && ! in_array($oldStatus, ['disetujui', 'dipinjam'])) {
                // Barang baru disetujui → kurangi stok
                if ($barang->stok < $request->jumlah) {
                    toast("Stok {$barang->nama} tidak mencukupi.", 'error');
                    return back()->withInput();
                }
                $barang->decrement('stok', $request->jumlah);
            } elseif (in_array($newStatus, ['selesai', 'dikembalikan']) && in_array($oldStatus, ['dipinjam', 'disetujui'])) {
                // Barang dikembalikan → tambah stok
                $barang->increment('stok', $peminjaman->jumlah);
            }
        }

        $peminjaman->update([
            'user_id'       => $request->user_id,
            'barang_id'     => $request->barang_id,
            'jumlah'        => (int) $request->jumlah,
            'tanggal'       => $request->tanggal,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status'        => $newStatus,
            'keterangan'    => $request->keterangan ?? '-',
        ]);

        Log::info('Peminjaman diupdate', [
            'id'          => $peminjaman->id,
            'status_lama' => $oldStatus,
            'status_baru' => $newStatus,
        ]);

        toast('Data peminjaman berhasil diperbarui.', 'success');
        return redirect()->route('backend.peminjaman.index');
    }

    /**
     * DESTROY
     */
    public function destroy($id)
    {
        $p = PeminjamanBarang::findOrFail($id);
        $p->delete();
        toast('Peminjaman dihapus', 'success');
        return back();
    }

    /**
     * HELPER: cek ketersediaan stok & waktu tumpang tindih
     */
    protected function checkAvailability($barangId, $tanggal, $mulai, $selesai, $jumlah, $excludeId = null)
    {
        $barang = Barang::find($barangId);
        if (! $barang) {
            return ['ok' => false, 'message' => 'Barang tidak ditemukan.'];
        }

        $query = PeminjamanBarang::where('barang_id', $barangId)
            ->whereDate('tanggal', $tanggal)
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
