<?php
namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanBarang;
use App\Models\VerifikasiPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiPeminjamanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'pic:pic']);
    }

    /**
     * Display list of peminjaman yang perlu diverifikasi
     */
    public function index(Request $request)
    {
        // 🔥 FIX: Ganti 'barang' jadi 'detailbarangs.barangRuangan.barang'
        $query = PeminjamanBarang::with([
            'user',
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
            'verifikasi.pic',
        ])
            ->where('status', 'dikembalikan');

        // Filter by status verifikasi
        if ($request->filled('status_verifikasi')) {
            if ($request->status_verifikasi === 'belum_verifikasi') {
                $query->doesntHave('verifikasi');
            } else {
                $query->whereHas('verifikasi', function ($q) use ($request) {
                    $q->where('status_verifikasi', $request->status_verifikasi);
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // 🔥 FIX: Search di detailbarangs -> barangRuangan -> barang
                $q->whereHas('detailbarangs.barangRuangan.barang', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%");
                })
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $peminjaman = $query->orderBy('tanggal_kembali', 'desc')->paginate(15);

        return view('pic.verifikasi-peminjaman.index', compact('peminjaman'));
    }

    /**
     * Show form to verify peminjaman
     */
    public function create($id)
    {
        // 🔥 FIX: Ganti 'barang' jadi 'detailbarangs.barangRuangan.barang'
        $peminjaman = PeminjamanBarang::with([
            'user',
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
            'verifikasi',
        ])
            ->findOrFail($id);

        // Check if already verified
        if ($peminjaman->isVerified()) {
            return redirect()
                ->route('pic.verifikasi-peminjaman.show', $id)
                ->with('info', 'Peminjaman ini sudah diverifikasi');
        }

        // Check if status is dikembalikan
        if ($peminjaman->status !== 'dikembalikan') {
            return redirect()
                ->route('pic.verifikasi-peminjaman.index')
                ->with('error', 'Hanya peminjaman dengan status "Dikembalikan" yang bisa diverifikasi');
        }

        return view('pic.verifikasi-peminjaman.create', compact('peminjaman'));
    }

    /**
     * Store verifikasi peminjaman
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'kondisi'     => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'catatan_pic' => 'nullable|string|max:1000',
            'foto_bukti'  => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'kondisi.required' => 'Kondisi barang harus dipilih',
            'kondisi.in'       => 'Kondisi barang tidak valid',
            'foto_bukti.image' => 'File harus berupa gambar',
            'foto_bukti.mimes' => 'Format gambar harus jpeg, jpg, atau png',
            'foto_bukti.max'   => 'Ukuran gambar maksimal 2MB',
        ]);

        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanBarang::findOrFail($id);

            // Check if already verified
            if ($peminjaman->isVerified()) {
                return back()->with('error', 'Peminjaman ini sudah diverifikasi');
            }

            // Upload foto bukti if exists
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $file     = $request->file('foto_bukti');
                $filename = 'verifikasi_peminjaman_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('verifikasi/peminjaman', $filename, 'public');
            }

            // Determine status verifikasi based on kondisi
            $statusVerifikasi = match ($validated['kondisi']) {
                'baik'         => 'diterima',
                'rusak_ringan' => 'pending',
                'rusak_berat', 'hilang' => 'perlu_tindakan',
            };

            // Create verifikasi
            $verifikasi = VerifikasiPeminjaman::create([
                'peminjaman_id'        => $id,
                'pic_id'               => Auth::id(),
                'kondisi'              => $validated['kondisi'],
                'catatan_pic'          => $validated['catatan_pic'],
                'foto_bukti'           => $fotoPath,
                'status_verifikasi'    => $statusVerifikasi,
                'tanggal_verifikasi'   => now(),
                'is_reported_to_admin' => true,
            ]);

            DB::commit();

            Log::info('Verifikasi Peminjaman Created by PIC', [
                'verifikasi_id' => $verifikasi->id,
                'peminjaman_id' => $id,
                'pic_id'        => Auth::id(),
                'kondisi'       => $validated['kondisi'],
                'status'        => $statusVerifikasi,
            ]);

            $message = $statusVerifikasi === 'diterima'
                ? 'Verifikasi berhasil! Barang dalam kondisi baik.'
                : 'Verifikasi berhasil! Laporan telah dikirim ke admin.';

            return redirect()
                ->route('pic.verifikasi-peminjaman.show', $id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating verifikasi peminjaman', [
                'peminjaman_id' => $id,
                'error'         => $e->getMessage(),
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
        $peminjaman = PeminjamanBarang::with([
            'user',
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
            'verifikasi.pic',
        ])
            ->findOrFail($id);

        if (! $peminjaman->isVerified()) {
            return redirect()
                ->route('pic.verifikasi-peminjaman.create', $id)
                ->with('info', 'Peminjaman ini belum diverifikasi');
        }

        return view('pic.verifikasi-peminjaman.show', compact('peminjaman'));
    }
}
