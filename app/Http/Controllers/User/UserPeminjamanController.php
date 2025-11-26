<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PeminjamanBarang;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPeminjamanController extends Controller
{
    protected $avail;

    public function __construct(AvailabilityService $avail)
    {
        $this->avail = $avail;
    }

    //create peminjaman
    public function create()
    {
        $barang = Barang::orderBy('nama', 'asc')->get();
        return view('peminjaman_create', compact('barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'       => 'required|exists:barangs,id',
            'jumlah'          => 'required|integer|min:1',
            'tanggal_pinjam'  => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i',
            'keterangan'      => 'nullable|string|max:255',
        ]);

        // parse
        $tanggalPinjam  = $request->tanggal_pinjam;
        $tanggalKembali = $request->tanggal_kembali;
        $waktuMulai     = $request->waktu_mulai;
        $waktuSelesai   = $request->waktu_selesai;

        $start = Carbon::parse("$tanggalPinjam $waktuMulai");
        $end   = Carbon::parse("$tanggalKembali $waktuSelesai");

        // if start in past
        if ($start->lt(now())) {
            toast('Waktu mulai sudah lewat! Pilih waktu yang valid.', 'error');
            return back()->withInput();
        }

        // if end <= start
        if ($end->lte($start)) {
            toast('Waktu selesai harus setelah waktu mulai.', 'error');
            return back()->withInput();
        }

        // tambahan: kalau pinjam **hari ini**, waktu_mulai harus >= sekarang (plus tolerance)
        if ($start->toDateString() === now()->toDateString()) {
            // compare times (H:i)
            $nowTime = now()->format('H:i');
            if ($waktuMulai <= $nowTime) {
                toast('Untuk peminjaman hari ini, waktu mulai harus lebih besar dari waktu sekarang.', 'error');
                return back()->withInput();
            }
        }

        $barang = Barang::findOrFail($request->barang_id);

        // check availability (MODE A: only approved/dipinjam considered)
        $cek = $this->avail->check(
            $barang->id,
            $tanggalPinjam,
            $tanggalKembali,
            $waktuMulai,
            $waktuSelesai
        );

        if (! $cek['status']) {
            toast($cek['message'], 'error');
            return back()->withInput();
        }

        if ((int) $request->jumlah > (int) $cek['available']) {
            toast("Hanya tersedia {$cek['available']} unit.", 'error');
            return back()->withInput();
        }

        PeminjamanBarang::create([
            'user_id'         => Auth::id(),
            'barang_id'       => $barang->id,
            'jumlah'          => (int) $request->jumlah,
            'tanggal_pinjam'  => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'waktu_mulai'     => $waktuMulai,
            'waktu_selesai'   => $waktuSelesai,
            'keterangan'      => $request->keterangan ?? '-',
            'status'          => 'menunggu', // IMPORTANT: sesuai migration
        ]);

        toast('Peminjaman berhasil diajukan, menunggu persetujuan.', 'success');
        return redirect()->route('peminjaman.create');
    }
}

