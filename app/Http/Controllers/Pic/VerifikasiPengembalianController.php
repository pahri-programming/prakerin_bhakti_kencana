<?php
namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Backend\PengembalianBarangController;
use App\Http\Controllers\Controller;
use App\Models\PengembalianBarang;
use App\Models\VerifikasiPengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiPengembalianController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'pic:pic']);
    }

    /**
     * Display list of pengembalian yang perlu diverifikasi
     */
    public function index(Request $request)
    {
        // Base query - ambil SEMUA yang ada barang bermasalah
        $query = PengembalianBarang::with([
            'peminjamanBarang.user',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic',
        ])
            ->whereHas('detailpengembalians', function ($q) {
                $q->where('status_awal', 'bermasalah');
            });

        // Filter by status verifikasi (HANYA kalau user pilih)
        if ($request->filled('status_verifikasi')) {
            if ($request->status_verifikasi === 'belum_verifikasi') {
                $query->where('status', 'menunggu_pic')->doesntHave('verifikasi');
            } elseif ($request->status_verifikasi === 'pending') {
                $query->whereHas('verifikasi', function ($q) {
                    $q->where('status_verifikasi', 'pending');
                });
            } elseif ($request->status_verifikasi === 'diterima') {
                $query->whereHas('verifikasi', function ($q) {
                    $q->where('status_verifikasi', 'diterima');
                });
            } elseif ($request->status_verifikasi === 'perlu_tindakan') {
                $query->whereHas('verifikasi', function ($q) {
                    $q->where('status_verifikasi', 'perlu_tindakan');
                });
            }
        }
        // NO DEFAULT FILTER - tampilkan SEMUA data dengan barang bermasalah

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('peminjamanBarang', function ($q2) use ($search) {
                    $q2->where('kode', 'like', "%{$search}%");
                })
                    ->orWhereHas('peminjamanBarang', function ($q2) use ($search) {
                        $q2->where('nama_peminjam', 'like', "%{$search}%");
                    })
                    ->orWhereHas('peminjamanBarang.user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('detailpengembalians.barang', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $pengembalian = $query->orderBy('tanggal_kembali', 'desc')->paginate(15);

        return view('pic.verifikasi-pengembalian.index', compact('pengembalian'));
    }

    /**
     * Show form to verify pengembalian
     */
    public function create($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang.user',
            'peminjamanBarang.detailbarangs.barangRuangan.barang',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi',
        ])->findOrFail($id);

        if ($pengembalian->isVerified()) {
            return redirect()
                ->route('pic.verifikasi-pengembalian.show', $id)
                ->with('info', 'Pengembalian ini sudah diverifikasi');
        }

        if ($pengembalian->status !== 'menunggu_pic') {
            return redirect()
                ->route('pic.verifikasi-pengembalian.index')
                ->with('error', 'Hanya pengembalian dengan status "Menunggu PIC" yang bisa diverifikasi');
        }

        return view('pic.verifikasi-pengembalian.create', compact('pengembalian'));
    }

    /**
     * Store verifikasi pengembalian dengan multiple photo upload
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'kondisi'      => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_pic'  => 'nullable|string|max:1000',
            'foto_bukti'   => 'nullable|array|max:6',
            'foto_bukti.*' => 'image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'kondisi.required'   => 'Kondisi barang harus dipilih',
            'kondisi.in'         => 'Kondisi barang tidak valid',
            'foto_bukti.array'   => 'Format foto tidak valid',
            'foto_bukti.max'     => 'Maksimal 6 foto',
            'foto_bukti.*.image' => 'File harus berupa gambar',
            'foto_bukti.*.mimes' => 'Format gambar harus jpeg, jpg, atau png',
            'foto_bukti.*.max'   => 'Ukuran gambar maksimal 2MB per file',
        ]);

        try {
            DB::beginTransaction();

            $pengembalian = PengembalianBarang::findOrFail($id);

            if ($pengembalian->isVerified()) {
                return back()->with('error', 'Pengembalian ini sudah diverifikasi');
            }

            // Upload multiple foto bukti
            $fotoPaths = [];
            if ($request->hasFile('foto_bukti')) {
                foreach ($request->file('foto_bukti') as $index => $file) {
                    $filename    = 'verifikasi_pengembalian_' . $id . '_' . ($index + 1) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path        = $file->storeAs('verifikasi/pengembalian', $filename, 'public');
                    $fotoPaths[] = $path;
                }
            }

            // Determine status verifikasi based on kondisi
            $statusVerifikasi = match ($validated['kondisi']) {
                'baik'         => 'diterima',
                'rusak_ringan' => 'pending',
                'rusak_berat', 'hilang' => 'perlu_tindakan',
            };

            // Create verifikasi with foto array
            $verifikasi = VerifikasiPengembalian::create([
                'pengembalian_barang_id' => $id,
                'pic_id'                 => Auth::id(),
                'kondisi'                => $validated['kondisi'],
                'catatan_pic'            => $validated['catatan_pic'],
                'foto_bukti'             => ! empty($fotoPaths) ? $fotoPaths : null,
                'status_verifikasi'      => $statusVerifikasi,
                'tanggal_verifikasi'     => now(),
                'is_reported_to_admin'   => true,
            ]);

            //  Commit dulu sebelum panggil updateStatusFromVerifikasi
            // supaya data verifikasi sudah tersimpan saat method itu query ke DB
            DB::commit();

            // Call admin controller method to update status & stok
            app(PengembalianBarangController::class)->updateStatusFromVerifikasi($id);

            Log::info('Verifikasi Pengembalian Created by PIC', [
                'verifikasi_id'     => $verifikasi->id,
                'pengembalian_id'   => $id,
                'pic_id'            => Auth::id(),
                'kondisi'           => $validated['kondisi'],
                'status_verifikasi' => $statusVerifikasi,
                'total_photos'      => count($fotoPaths),
            ]);

            $message = match ($statusVerifikasi) {
                'diterima'       => 'Verifikasi berhasil! Barang dalam kondisi baik, stok sudah dikembalikan.',
                'pending'        => 'Verifikasi berhasil! Barang rusak ringan, laporan dikirim ke admin.',
                'perlu_tindakan' => 'Verifikasi berhasil! Barang rusak berat/hilang, perlu tindakan admin segera.',
            };

            return redirect()
                ->route('pic.verifikasi-pengembalian.show', $id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating verifikasi pengembalian', [
                'pengembalian_id' => $id,
                'error'           => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Show verifikasi detail
     */
    public function show($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang.user',
            'peminjamanBarang.detailbarangs.barangRuangan.barang',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic',
        ])->findOrFail($id);

        if (! $pengembalian->isVerified()) {
            return redirect()
                ->route('pic.verifikasi-pengembalian.create', $id)
                ->with('info', 'Pengembalian ini belum diverifikasi');
        }

        return view('pic.verifikasi-pengembalian.show', compact('pengembalian'));
    }
}
