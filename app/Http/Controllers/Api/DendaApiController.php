<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DendaBooking;
use App\Models\DendaPengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DendaApiController extends Controller
{
    /**
     * GET /api/denda
     * Semua denda milik user (barang + booking digabung)
     * Diurutkan dari yang terbaru
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Denda dari peminjaman barang
        $dendaBarang = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang',
            'verifikasiPengembalian',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get()
            ->map(fn($d) => $this->formatDendaBarang($d));

        // Denda dari booking ruangan
        $dendaBooking = DendaBooking::with([
            'booking.ruangan',
            'verifikasiBooking',
        ])
            ->whereHas('booking', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get()
            ->map(fn($d) => $this->formatDendaBooking($d));

        // Gabungkan & urutkan dari terbaru
        $semua = $dendaBarang->concat($dendaBooking)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'success' => true,
            'total'   => $semua->count(),
            'data'    => $semua,
        ], 200);
    }

    /**
     * GET /api/denda/{type}/{id}
     * Detail denda
     * type: "barang" atau "booking"
     */
    public function show(Request $request, $type, $id)
    {
        $userId = $request->user()->id;

        if ($type === 'barang') {
            $denda = DendaPengembalian::with([
                'pengembalianBarang.peminjamanBarang',
                'verifikasiPengembalian',
            ])
                ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->find($id);

            if (! $denda) {
                return response()->json(['success' => false, 'message' => 'Denda tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $this->formatDendaBarang($denda),
            ], 200);
        }

        if ($type === 'booking') {
            $denda = DendaBooking::with([
                'booking.ruangan',
                'verifikasiBooking',
            ])
                ->whereHas('booking', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->find($id);

            if (! $denda) {
                return response()->json(['success' => false, 'message' => 'Denda tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $this->formatDendaBooking($denda),
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Tipe denda tidak valid. Gunakan "barang" atau "booking"'], 422);
    }

    /**
     * POST /api/denda/{type}/{id}/upload-bukti
     * User upload bukti pembayaran
     * type: "barang" atau "booking"
     */
    public function uploadBukti(Request $request, $type, $id)
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

        $userId = $request->user()->id;

        // Tentukan model berdasarkan type
        if ($type === 'barang') {
            $denda = DendaPengembalian::whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->find($id);
        } elseif ($type === 'booking') {
            $denda = DendaBooking::whereHas('booking', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->find($id);
        } else {
            return response()->json(['success' => false, 'message' => 'Tipe denda tidak valid'], 422);
        }

        if (! $denda) {
            return response()->json(['success' => false, 'message' => 'Denda tidak ditemukan'], 404);
        }

        // Validasi status
        if ($denda->isBayar()) {
            return response()->json(['success' => false, 'message' => 'Denda ini sudah lunas'], 422);
        }

        if ($denda->isDibebaskan()) {
            return response()->json(['success' => false, 'message' => 'Denda ini sudah dibebaskan, tidak perlu membayar'], 422);
        }

        if ($denda->status_pembayaran === 'menunggu_verifikasi') {
            return response()->json(['success' => false, 'message' => 'Bukti sudah diupload, sedang menunggu verifikasi admin'], 422);
        }

        try {
            $path = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');

            $denda->update([
                'bukti_pembayaran'      => $path,
                'tanggal_bayar'         => $request->tanggal_bayar,
                'keterangan_pembayaran' => $request->keterangan_pembayaran,
                'status_pembayaran'     => 'menunggu_verifikasi',
            ]);

            Log::info('Bukti pembayaran denda diupload', [
                'type'     => $type,
                'denda_id' => $denda->id,
                'user_id'  => $userId,
            ]);

            $formatted = $type === 'barang'
                ? $this->formatDendaBarang($denda->fresh(['pengembalianBarang.peminjamanBarang', 'verifikasiPengembalian']))
                : $this->formatDendaBooking($denda->fresh(['booking.ruangan', 'verifikasiBooking']));

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
                'data'    => $formatted,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Upload bukti denda error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal upload bukti: ' . $e->getMessage()], 500);
        }
    }

    // ============================================================
    // PRIVATE HELPERS
    // ============================================================

    private function formatDendaBarang($d)
    {
        return [
            'id'                    => $d->id,
            'type'                  => 'barang', // ← Flutter bisa bedain dari sini
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
            'created_at'            => $d->created_at,
            'referensi'             => [
                'label' => 'Peminjaman Barang',
                'kode'  => $d->pengembalianBarang->peminjamanBarang->kode ?? null,
            ],
        ];
    }

    private function formatDendaBooking($d)
    {
        return [
            'id'                    => $d->id,
            'type'                  => 'booking', // ← Flutter bisa bedain dari sini
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
            'kondisi_ruangan'       => $d->verifikasiBooking->kondisi_ruangan ?? null,
            'created_at'            => $d->created_at,
            'referensi'             => [
                'label'        => 'Booking Ruangan',
                'kode'         => $d->booking->kode ?? null,
                'nama_ruangan' => $d->booking->ruangan->nama_ruangan ?? null,
            ],
        ];
    }
}
