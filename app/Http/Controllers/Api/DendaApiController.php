<?php
namespace App\Http\Controllers\Api;
    
use App\Http\Controllers\Controller;
use App\Models\DendaBooking;
use App\Models\DendaPengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // ← tambah ini
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
        // return response()->json([
        //     'has_file'   => $request->hasFile('bukti_pembayaran'),
        //     'all_files'  => array_keys($request->allFiles()),
        //     'all_fields' => $request->keys(),
        // ]);

        $request->validate([
            'bukti_pembayaran'      => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'tanggal_bayar'         => 'required|date',
            'keterangan_pembayaran' => 'nullable|string|max:500',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran harus diupload',
            'bukti_pembayaran.file'     => 'File harus berupa gambar',
            'bukti_pembayaran.mimes'    => 'Format harus jpg, jpeg, atau png',
            'bukti_pembayaran.max'      => 'Ukuran maksimal 2MB',
        ]);

        // Validasi AI — cek apakah file benar-benar bukti transfer
        $validasi = $this->validasiBuktiTransferAI($request->file('bukti_pembayaran'));
        if ($validasi['valid'] === false) {
            return response()->json([
                'success' => false,
                'message' => 'Foto ditolak: ' . $validasi['alasan'] . ' Pastikan upload screenshot bukti transfer bank atau e-wallet.',
            ], 422);
        }

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

    private function validasiBuktiTransferAI($file): array
    {
        try {
            $apiKey = config('services.anthropic.api_key');

            if (empty($apiKey)) {
                Log::warning('ANTHROPIC_API_KEY tidak dikonfigurasi, validasi AI dilewati.');
                return ['valid' => true, 'alasan' => 'Validasi AI tidak tersedia'];
            }

            $base64    = base64_encode(file_get_contents($file->getRealPath()));
            $mediaType = $file->getMimeType();

            $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (! in_array($mediaType, $allowedMime)) {
                return ['valid' => false, 'alasan' => 'Format file tidak didukung.'];
            }

            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])->timeout(20)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 100,
                'messages'   => [[
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'   => 'image',
                            'source' => [
                                'type'       => 'base64',
                                'media_type' => $mediaType,
                                'data'       => $base64,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Analisis gambar ini dengan ketat. Jawab HANYA dengan JSON tanpa teks lain.

                            TOLAK (valid:false) jika gambar adalah:
                            - Foto orang/wajah/selfie
                            - Foto benda fisik (barang, makanan, dll)
                            - Screenshot media sosial/chat/WhatsApp
                            - Gambar tidak jelas atau kosong
                            - Foto dokumen bukan pembayaran

                            TERIMA (valid:true) HANYA jika gambar adalah:
                            - Screenshot mobile banking (BCA/BNI/BRI/Mandiri/BSI dll) yang menampilkan nominal & tanggal transaksi
                            - Screenshot e-wallet (GoPay/OVO/Dana/ShopeePay) konfirmasi pembayaran
                            - Bukti QRIS berhasil dengan nominal
                            - Struk ATM fisik

                            Respond ONLY with this exact JSON format:
                            {"valid": true, "alasan": "bukti transfer valid"}
                            or
                            {"valid": false, "alasan": "alasan penolakan singkat"}',
                        ],
                    ],
                ]],
            ]);

            if (! $response->successful()) {
                Log::warning('Claude API gagal: ' . $response->status());
                return ['valid' => true, 'alasan' => 'Skip validasi server'];
            }

            $text = $response->json('content.0.text') ?? '';
            $text = trim(preg_replace('/```json|```/i', '', $text));

            preg_match('/\{[^{}]*"valid"[^{}]*\}/s', $text, $match);

            if (empty($match[0])) {
                return ['valid' => false, 'alasan' => 'Tidak bisa memvalidasi gambar, coba upload ulang.'];
            }

            $result = json_decode($match[0], true);

            if (json_last_error() !== JSON_ERROR_NONE || ! isset($result['valid'])) {
                return ['valid' => false, 'alasan' => 'Format respons tidak valid, coba lagi.'];
            }

            Log::info('Validasi bukti API result: ' . json_encode($result));

            return [
                'valid'  => (bool) $result['valid'],
                'alasan' => $result['alasan'] ?? '-',
            ];

        } catch (\Exception $e) {
            Log::error('Exception validasi AI API: ' . $e->getMessage());
            return ['valid' => false, 'alasan' => 'Terjadi kesalahan validasi, coba lagi.'];
        }
    }
}
