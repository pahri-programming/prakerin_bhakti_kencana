<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DendaPengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserDendaController extends Controller
{
    /**
     * LIST denda milik user yang sedang login
     */
    public function index()
    {
        $dendas = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang',
            'pengembalianBarang.detailpengembalians.barang',
            'verifikasiPengembalian',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->get();

        $stats = [
            // ✅ FIX: include menunggu_verifikasi karena admin belum approve = masih aktif
            'total_tagihan' => $dendas
                ->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi'])
                ->sum('jumlah_denda'),

            'total_lunas'   => $dendas
                ->where('status_pembayaran', 'sudah_bayar')
                ->sum('jumlah_denda'),

            // ✅ FIX: jumlah tagihan belum selesai
            'jumlah_aktif'  => $dendas
                ->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi'])
                ->count(),
        ];

        return view('user.denda.index', compact('dendas', 'stats'));
    }

    /**
     * DETAIL denda + form upload bukti bayar
     * (tidak ada perubahan)
     */
    public function show($id)
    {
        $denda = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang.user',
            'pengembalianBarang.detailpengembalians.barang',
            'verifikasiPengembalian.pic',
            'admin',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        // Decode rincian perhitungan jika ada
        $rincian = is_array($denda->rincian_perhitungan) ? $denda->rincian_perhitungan : [];

        return view('user.denda.show', compact('denda', 'rincian'));
    }

    /**
     * UPLOAD bukti pembayaran oleh user
     * ✅ TAMBAHAN: validasi AI server-side
     */
    public function uploadBukti(Request $request, $id)
    {
        $denda = DendaPengembalian::whereHas('pengembalianBarang.peminjamanBarang', function ($q) {
            $q->where('user_id', Auth::id());
        })->findOrFail($id);

        // Guard status
        if ($denda->isBayar()) {
            return back()->withErrors(['error' => 'Denda ini sudah lunas.']);
        }

        if ($denda->isDibebaskan()) {
            return back()->withErrors(['error' => 'Denda ini sudah dibebaskan, tidak perlu dibayar.']);
        }

        if ($denda->status_pembayaran === 'menunggu_verifikasi') {
            return back()->withErrors(['error' => 'Bukti sudah dikirim, sedang menunggu verifikasi admin.']);
        }

        // Validasi form
        $request->validate([
            'bukti_pembayaran'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'keterangan_pembayaran' => 'nullable|string|max:500',
            'tanggal_bayar'         => 'required|date|before_or_equal:today',
        ], [
            'bukti_pembayaran.required'     => 'Bukti pembayaran wajib diupload.',
            'bukti_pembayaran.image'        => 'File harus berupa gambar (JPG/PNG).',
            'bukti_pembayaran.max'          => 'Ukuran file maksimal 2MB.',
            'tanggal_bayar.required'        => 'Tanggal bayar wajib diisi.',
            'tanggal_bayar.before_or_equal' => 'Tanggal bayar tidak boleh lebih dari hari ini.',
        ]);

        // ✅ Validasi AI server-side — tolak jika bukan bukti transfer
        $validasi = $this->validasiBuktiTransferAI($request->file('bukti_pembayaran'));

        if ($validasi['valid'] === false) {
            return back()->withErrors([
                'bukti_pembayaran' => 'Foto ditolak: ' . $validasi['alasan']
                . ' Pastikan upload screenshot bukti transfer bank atau e-wallet.',
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            if ($denda->bukti_pembayaran) {
                Storage::disk('public')->delete($denda->bukti_pembayaran);
            }

            $path = $request->file('bukti_pembayaran')
                ->store('bukti-pembayaran/user', 'public');

            $denda->update([
                'bukti_pembayaran'      => $path,
                'tanggal_bayar'         => $request->tanggal_bayar,
                'keterangan_pembayaran' => $request->keterangan_pembayaran,
                'status_pembayaran'     => 'menunggu_verifikasi',
            ]);

            DB::commit();

            Log::info("User #" . Auth::id() . " upload bukti bayar denda #{$denda->id}");

            return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error upload bukti denda: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ─── PRIVATE HELPER ──────────────────────────────────────────────────────

    /**
     * Validasi gambar via Claude Haiku API
     * Return: ['valid' => bool, 'alasan' => string]
     */
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

            // Pastikan mime type valid untuk Anthropic
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
                            // ← Prompt lebih tegas, eksplisit reject foto orang
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

            Log::info('Claude API response status: ' . $response->status());
            Log::info('Claude API response body: ' . $response->body());
            if (! $response->successful()) {
                return ['valid' => true, 'alasan' => 'Skip validasi server'];
            }

            $text = $response->json('content.0.text') ?? '';
            Log::info('Claude API text response: ' . $text);

            // Bersihkan response dari markdown code block kalau ada
            $text = preg_replace('/```json|```/i', '', $text);
            $text = trim($text);

            // Cari JSON di dalam response
            preg_match('/\{[^{}]*"valid"[^{}]*\}/s', $text, $match);

            if (empty($match[0])) {
                Log::warning('Claude response tidak ada JSON valid: ' . $text);
                return ['valid' => false, 'alasan' => 'Tidak bisa memvalidasi gambar, coba upload ulang.'];
            }

            $result = json_decode($match[0], true);

            if (json_last_error() !== JSON_ERROR_NONE || ! isset($result['valid'])) {
                Log::warning('JSON parse error: ' . $match[0]);
                return ['valid' => false, 'alasan' => 'Format respons tidak valid, coba lagi.'];
            }

            Log::info('Validasi bukti result: ' . json_encode($result));

            return [
                'valid'  => (bool) $result['valid'],
                'alasan' => $result['alasan'] ?? '-',
            ];

        } catch (\Exception $e) {
            Log::error('Exception validasi AI: ' . $e->getMessage());
            // ← Kalau exception, TOLAK dulu
            return ['valid' => false, 'alasan' => 'Terjadi kesalahan validasi, coba lagi.'];
        }
    }
}
