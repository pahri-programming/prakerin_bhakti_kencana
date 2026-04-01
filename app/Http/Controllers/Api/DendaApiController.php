<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DendaPengembalian;
use App\Models\PengembalianBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DendaApiController extends Controller
{
    /**
     * GET /api/denda
     * Daftar semua denda milik user yang login
     */
    public function index(Request $request)
    {
        $dendas = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang',
            'verifikasiPengembalian',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->latest()
            ->get();

        $data = $dendas->map(fn($d) => $this->formatDenda($d));

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/denda/{id}
     * Detail denda + info pembayaran
     */
    public function show(Request $request, $id)
    {
        $denda = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang',
            'verifikasiPengembalian',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })
            ->find($id);

        if (! $denda) {
            return response()->json([
                'success' => false,
                'message' => 'Denda tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatDenda($denda),
        ], 200);
    }

    /**
     * POST /api/denda/{id}/upload-bukti
     * User upload bukti pembayaran denda
     *
     * Body: multipart/form-data
     * - bukti_pembayaran: file image
     * - tanggal_bayar: date
     * - keterangan_pembayaran: string (optional)
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'tanggal_bayar'         => 'required|date',
            'keterangan_pembayaran' => 'nullable|string|max:500',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran harus diupload',
            'bukti_pembayaran.image'    => 'File harus berupa gambar',
            'bukti_pembayaran.max'      => 'Ukuran maksimal 2MB',
        ]);

        // Cek denda milik user ini
        $denda = DendaPengembalian::whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->find($id);

        if (! $denda) {
            return response()->json([
                'success' => false,
                'message' => 'Denda tidak ditemukan',
            ], 404);
        }

        if ($denda->isBayar()) {
            return response()->json([
                'success' => false,
                'message' => 'Denda ini sudah lunas',
            ], 422);
        }

        if ($denda->isDibebaskan()) {
            return response()->json([
                'success' => false,
                'message' => 'Denda ini sudah dibebaskan, tidak perlu membayar',
            ], 422);
        }

        if ($denda->status_pembayaran === 'menunggu_verifikasi') {
            return response()->json([
                'success' => false,
                'message' => 'Bukti sudah diupload, sedang menunggu verifikasi admin',
            ], 422);
        }

        try {
            // Upload foto bukti
            $path = $request->file('bukti_pembayaran')
                ->store('bukti-pembayaran', 'public');

            // Update denda — status jadi menunggu_verifikasi
            // Admin nanti yang konfirmasi lunas
            $denda->update([
                'bukti_pembayaran'      => $path,
                'tanggal_bayar'         => $request->tanggal_bayar,
                'keterangan_pembayaran' => $request->keterangan_pembayaran,
                'status_pembayaran'     => 'menunggu_verifikasi',
            ]);

            Log::info('Bukti pembayaran denda diupload user', [
                'denda_id' => $denda->id,
                'user_id'  => $request->user()->id,
                'path'     => $path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
                'data'    => $this->formatDenda($denda->fresh()),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Upload bukti denda error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload bukti: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper: Format data denda untuk response JSON
     */
    private function formatDenda($d)
    {
        return [
            'id'                    => $d->id,
            'jumlah_denda'          => $d->jumlah_denda,
            'jumlah_denda_format'   => $d->jumlah_denda_format,
            'status_pembayaran'     => $d->status_pembayaran,
            'status_label'          => $d->status_pembayaran_label,
            'tindakan_admin'        => $d->tindakan_admin,
            'keterangan_denda'      => $d->keterangan_denda,
            'tanggal_tindakan'      => $d->tanggal_tindakan,
            'tanggal_bayar'         => $d->tanggal_bayar,
            'bukti_pembayaran'      => $d->bukti_pembayaran
                ? asset('storage/' . $d->bukti_pembayaran)
                : null,
            'keterangan_pembayaran' => $d->keterangan_pembayaran,
            'kondisi_barang'        => $d->verifikasiPengembalian->kondisi ?? null,
            'peminjaman'            => [
                'kode' => $d->pengembalianBarang->peminjamanBarang->kode ?? null,
            ],
        ];
    }
}
