<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangRuangan;
use App\Models\DetailPeminjamanBarang;
use App\Models\PeminjamanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeminjamanApiController extends Controller
{
    /**
     * GET /api/peminjaman
     * Daftar semua peminjaman milik user yang login
     */
    public function index(Request $request)
    {
        $peminjamans = PeminjamanBarang::with([
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $data = $peminjamans->map(function ($p) {
            return $this->formatPeminjaman($p);
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * POST /api/peminjaman
     * Ajukan peminjaman baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pinjam'      => 'required|date|after_or_equal:today',
            'tanggal_kembali'     => 'required|date|after_or_equal:tanggal_pinjam',
            'keterangan'          => 'nullable|string|max:500',
            'barang_ruangan_id'   => 'required|array|min:1',
            'barang_ruangan_id.*' => 'required|exists:barang_ruangans,id',
            'jumlah'              => 'required|array|min:1',
            'jumlah.*'            => 'required|integer|min:1',
        ]);

        // Validasi stok setiap barang
        $detailBarangs = [];
        foreach ($request->barang_ruangan_id as $index => $barangRuanganId) {
            $jumlah        = $request->jumlah[$index];
            $barangRuangan = BarangRuangan::with(['barang', 'ruangan'])->find($barangRuanganId);

            if (! $barangRuangan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang ruangan tidak ditemukan',
                ], 404);
            }

            if ($barangRuangan->status !== 'tersedia') {
                return response()->json([
                    'success' => false,
                    'message' => "{$barangRuangan->barang->nama} di {$barangRuangan->ruangan->nama_ruangan} sedang tidak tersedia",
                ], 422);
            }

            if ($jumlah > $barangRuangan->qty) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok {$barangRuangan->barang->nama} tidak cukup. Tersedia {$barangRuangan->qty} unit",
                ], 422);
            }

            $detailBarangs[] = [
                'barang_ruangan_id' => $barangRuanganId,
                'jumlah'            => $jumlah,
            ];
        }

        DB::beginTransaction();
        try {
            $peminjaman = PeminjamanBarang::create([
                'user_id'         => $request->user()->id,
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

            $peminjaman->load([
                'detailbarangs.barangRuangan.barang',
                'detailbarangs.barangRuangan.ruangan',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil diajukan',
                'data'    => $this->formatPeminjaman($peminjaman),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API store peminjaman error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan peminjaman: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/peminjaman/{id}
     * Detail peminjaman milik user
     */
    public function show(Request $request, $id)
    {
        $peminjaman = PeminjamanBarang::with([
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (! $peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatPeminjaman($peminjaman),
        ], 200);
    }

    /**
     * DELETE /api/peminjaman/{id}
     * Batalkan peminjaman (hanya yang masih menunggu)
     */
    public function destroy(Request $request, $id)
    {
        $peminjaman = PeminjamanBarang::where('user_id', $request->user()->id)->find($id);

        if (! $peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak ditemukan',
            ], 404);
        }

        if ($peminjaman->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak bisa dibatalkan karena sudah diproses',
            ], 422);
        }

        $peminjaman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan',
        ], 200);
    }

    /**
     * Helper: Format data peminjaman untuk response JSON
     */
    private function formatPeminjaman($p)
    {
        return [
            'id'              => $p->id,
            'kode'            => $p->kode,
            'status'          => $p->status,
            'alasan_tolak'    => $p->alasan_tolak,
            'tanggal_pinjam'  => $p->tanggal_pinjam,
            'tanggal_kembali' => $p->tanggal_kembali,
            'durasi_hari'     => \Carbon\Carbon::parse($p->tanggal_pinjam)
                ->diffInDays($p->tanggal_kembali),
            'keterangan'      => $p->keterangan,
            'created_at'      => $p->created_at,
            'barang'          => $p->detailbarangs->map(function ($d) {
                return [
                    'detail_id'         => $d->id,
                    'barang_ruangan_id' => $d->barang_ruangan_id,
                    'nama_barang'       => $d->barangRuangan->barang->nama ?? '-',
                    'nama_ruangan'      => $d->barangRuangan->ruangan->nama_ruangan ?? '-',
                    'jumlah'            => $d->jumlah,
                ];
            }),
        ];
    }
}
