<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BarangRuangan;
use App\Models\DetailPeminjamanBarang;
use App\Models\PeminjamanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPeminjamanController extends Controller
{
    /**
     *
     */
    public function index()
    {
        $peminjamans = PeminjamanBarang::with([
            'details.barangRuangan.barang',
            'details.barangRuangan.ruangan',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.peminjaman.index', compact('peminjamans'));
    }

    /**
     * FORM CREATE
     */
    public function create()
    {
        $barangRuangans = BarangRuangan::with(['barang', 'ruangan'])
            ->where('status', 'tersedia')
            ->where('qty', '>', 0)
            ->orderBy('id', 'asc')
            ->get();

        $barangData = $barangRuangans->map(fn($br) => [
            'id'           => $br->id,
            'ruangan_id'   => $br->ruangan_id,
            'ruangan_nama' => $br->ruangan->nama_ruangan ?? '-',
            'barang_nama'  => $br->barang->nama ?? '-',
            'qty'          => $br->qty,
        ])->values();

        return view('user.peminjaman.create', compact('barangRuangans','barangData'));
    }

    /**
     * STORE PEMINJAMAN (MULTI BARANG)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pinjam'      => 'required|date',
            'tanggal_kembali'     => 'required|date|after_or_equal:tanggal_pinjam',
            'keterangan'          => 'nullable|string',

            'barang_ruangan_id'   => 'required|array|min:1',
            'barang_ruangan_id.*' => 'required|exists:barang_ruangans,id',

            'jumlah'              => 'required|array|min:1',
            'jumlah.*'            => 'required|integer|min:1',
        ]);

        $detailBarangs = [];

        foreach ($request->barang_ruangan_id as $index => $barangRuanganId) {
            $jumlah = $request->jumlah[$index];

            $barangRuangan = BarangRuangan::with(['barang', 'ruangan'])
                ->findOrFail($barangRuanganId);

            // ❗ VALIDASI STATUS
            if ($barangRuangan->status !== 'tersedia') {
                return back()->withErrors([
                    'barang' => "{$barangRuangan->barang->nama} sedang tidak tersedia",
                ])->withInput();
            }

            // ❗ VALIDASI STOK
            if ($jumlah > $barangRuangan->qty) {
                return back()->withErrors([
                    'jumlah' => "Stok {$barangRuangan->barang->nama} tidak cukup. Sisa {$barangRuangan->qty}",
                ])->withInput();
            }

            $detailBarangs[] = [
                'barang_ruangan_id' => $barangRuanganId,
                'jumlah'            => $jumlah,
            ];
        }

        DB::beginTransaction();

        try {
            $peminjaman = PeminjamanBarang::create([
                'user_id'         => Auth::id(),
                'tanggal_pinjam'  => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'keterangan'      => $request->keterangan,
                'status'          => 'menunggu',
            ]);

            foreach ($detailBarangs as $detail) {
                DetailPeminjamanBarang::create([
                    'peminjaman_barang_id' => $peminjaman->id,
                    'barang_ruangan_id'    => $detail['barang_ruangan_id'],
                    'jumlah'               => $detail['jumlah'],
                ]);
            }

            DB::commit();

            return redirect()->route('user.peminjaman.index')
                ->with('success', 'Peminjaman berhasil diajukan');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     *
     */
    public function show($id)
    {
        $peminjaman = PeminjamanBarang::with([
            'details.barangRuangan.barang',
            'details.barangRuangan.ruangan',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.peminjaman.show', compact('peminjaman'));
    }

    /**
     *
     */
    public function destroy($id)
    {
        $peminjaman = PeminjamanBarang::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($peminjaman->status !== 'menunggu') {
            return back()->withErrors([
                'error' => 'Peminjaman tidak bisa dibatalkan',
            ]);
        }

        $peminjaman->delete();

        return back()->with('success', 'Peminjaman dibatalkan');
    }
}
