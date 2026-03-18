<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangRuangan;
use App\Models\DetailPengembalianBarang;
use App\Models\PeminjamanBarang;
use App\Models\PengembalianBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengembalianBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Export PDF
     */
    public function export()
    {
        $data = PengembalianBarang::with([
            'peminjamanBarang.user',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic', // ✅ Tambah relasi verifikasi
        ])
            ->latest()
            ->get()
            ->map(function ($p) {
                $p->tanggal_kembali_format = Carbon::parse($p->tanggal_kembali)->translatedFormat('d F Y');
                return $p;
            });

        $pdf = Pdf::loadView('backend.pengembalian.pdf', compact('data'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-pengembalian-' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Index - Daftar semua pengembalian
     */
    public function index(Request $request)
    {
        $query = PengembalianBarang::with([
            'peminjamanBarang.user',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic', // ✅ Tambah relasi verifikasi
        ]);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('peminjamanBarang', function ($q2) use ($search) {
                    $q2->where('kode', 'like', "%{$search}%")
                        ->orWhere('nama_peminjam', 'like', "%{$search}%");
                })
                    ->orWhereHas('peminjamanBarang.user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by tanggal kembali
        if ($request->filled('tanggal_kembali')) {
            $query->whereDate('tanggal_kembali', $request->tanggal_kembali);
        }

        // ✅ UPDATED: Filter by status baru
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengembalian = $query->latest()->paginate(10);

        $title = 'Data Pengembalian Barang';
        $text  = "Apakah anda yakin ingin menghapus data pengembalian ini?";
        confirmDelete($title, $text);

        return view('backend.pengembalian.index', compact('pengembalian'));
    }

    /**
     * Create - Form tambah pengembalian
     */
    public function create()
    {
        $peminjamans = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan'])
            ->where('status', 'disetujui')
            ->whereDoesntHave('pengembalianbarangs')
            ->latest()
            ->get();

        return view('backend.pengembalian.create', compact('peminjamans'));
    }

    /**
     * ✅ UPDATED: Store - Simpan pengembalian baru (OPSI 1)
     * Admin hanya cek status awal: baik atau bermasalah
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'peminjaman_barang_id' => 'required|exists:peminjaman_barangs,id',
                'barang_ruangan_id'    => 'required|exists:barang_ruangans,id',
                'tanggal_kembali'      => 'required|date',
                'keterangan'           => 'nullable|string|max:500',
                'barang_id'            => 'required|array|min:1',
                'barang_id.*'          => 'required|exists:barangs,id',
                'jumlah'               => 'required|array|min:1',
                'jumlah.*'             => 'required|integer|min:1',
                // ✅ UPDATED: Ganti kondisi jadi status_awal
                'status_awal'          => 'required|array|min:1',
                'status_awal.*'        => 'required|in:baik,bermasalah',
            ], [
                'required' => ':attribute harus diisi',
                'exists'   => ':attribute tidak valid',
                'integer'  => ':attribute harus berupa angka',
                'min'      => ':attribute minimal :min',
                'array'    => ':attribute harus berupa array',
                'date'     => ':attribute harus berupa tanggal',
                'in'       => ':attribute tidak valid',
            ]);

            // Validasi peminjaman
            $peminjaman = PeminjamanBarang::with('detailbarangs')->findOrFail($validated['peminjaman_barang_id']);

            if ($peminjaman->status !== 'disetujui') {
                toast('Peminjaman harus berstatus disetujui untuk dikembalikan.', 'error');
                return back()->withInput();
            }

            if ($peminjaman->hasReturn()) {
                toast('Peminjaman ini sudah memiliki data pengembalian.', 'error');
                return back()->withInput();
            }

            // Validasi detail barang
            $detailBarangs       = [];
            $hasProblematicItems = false;

            foreach ($validated['barang_id'] as $index => $barangId) {
                $jumlah     = $validated['jumlah'][$index];
                $statusAwal = $validated['status_awal'][$index];

                $detailBarangs[] = [
                    'barang_id'   => $barangId,
                    'jumlah'      => $jumlah,
                    'status_awal' => $statusAwal,
                ];

                // ✅ Cek apakah ada barang bermasalah
                if ($statusAwal === 'bermasalah') {
                    $hasProblematicItems = true;
                }
            }

            DB::beginTransaction();

            // ✅ UPDATED: Tentukan status berdasarkan status awal barang
            $status = $hasProblematicItems ? 'menunggu_pic' : 'dikembalikan';

            // Create pengembalian
            $pengembalian = PengembalianBarang::create([
                'peminjaman_barang_id' => $validated['peminjaman_barang_id'],
                'barang_ruangan_id'    => $validated['barang_ruangan_id'],
                'tanggal_kembali'      => $validated['tanggal_kembali'],
                'status'               => $status, // ✅ Status otomatis
                'keterangan'           => $validated['keterangan'] ?? null,
            ]);

            // Create detail pengembalian
            $detailCount = 0;
            foreach ($detailBarangs as $detail) {
                $detailPengembalian = DetailPengembalianBarang::create([
                    'pengembalian_barang_id' => $pengembalian->id,
                    'barang_id'              => $detail['barang_id'],
                    'jumlah'                 => $detail['jumlah'],
                    'status_awal'            => $detail['status_awal'], // ✅ UPDATED
                ]);

                if ($detailPengembalian) {
                    $detailCount++;
                }
            }

            // ✅ UPDATED: Jika semua baik, langsung kembalikan stok
            if (! $hasProblematicItems) {
                foreach ($peminjaman->detailbarangs as $detailPeminjaman) {
                    $barangRuangan = BarangRuangan::where('id', $detailPeminjaman->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        $barangRuangan->increment('qty', $detailPeminjaman->jumlah);

                        if ($barangRuangan->qty > 0) {
                            $barangRuangan->update(['status' => 'tersedia']);
                        }
                    }
                }

                // Update status peminjaman menjadi dikembalikan
                $peminjaman->update(['status' => 'dikembalikan']);
            }

            DB::commit();

            Log::info('Pengembalian barang berhasil dibuat', [
                'id'                    => $pengembalian->id,
                'peminjaman'            => $peminjaman->kode,
                'jumlah_item'           => $detailCount,
                'has_problematic_items' => $hasProblematicItems,
                'status'                => $status,
            ]);

            // ✅ UPDATED: Pesan berbeda berdasarkan kondisi
            if ($hasProblematicItems) {
                toast(
                    'Pengembalian berhasil dicatat. Ada barang bermasalah, menunggu verifikasi PIC.',
                    'warning'
                );
            } else {
                toast(
                    'Pengembalian berhasil dicatat. Semua barang dalam kondisi baik, stok sudah dikembalikan.',
                    'success'
                );
            }

            return redirect()->route('backend.pengembalian.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validasi gagal saat membuat pengembalian', [
                'errors' => $e->errors(),
            ]);
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat membuat pengembalian', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            toast('Gagal membuat pengembalian: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * Show - Detail pengembalian
     */
    public function show($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang.user',
            'peminjamanBarang.detailbarangs.barangRuangan.barang',
            'peminjamanBarang.detailbarangs.barangRuangan.ruangan',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic', // ✅ Tambah relasi verifikasi
        ])->findOrFail($id);

        $pengembalian->tanggal_kembali_format = Carbon::parse($pengembalian->tanggal_kembali)
            ->translatedFormat('d F Y');

        return view('backend.pengembalian.show', compact('pengembalian'));
    }

    /**
     * Edit - Form edit pengembalian
     */
    public function edit($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang',
            'barangRuangan',
            'detailpengembalians',
            'verifikasi', // ✅ Tambah relasi verifikasi
        ])->findOrFail($id);

        // ✅ Tidak bisa edit jika sudah diverifikasi PIC
        if ($pengembalian->isVerified()) {
            toast('Pengembalian yang sudah diverifikasi PIC tidak dapat diedit.', 'error');
            return redirect()->route('backend.pengembalian.show', $id);
        }

        $barangs        = Barang::orderBy('nama')->get();
        $barangRuangans = BarangRuangan::with(['barang', 'ruangan'])->get();

        return view('backend.pengembalian.edit', compact('pengembalian', 'barangs', 'barangRuangans'));
    }

    /**
     * ✅ UPDATED: Update - Update pengembalian (hanya tanggal & keterangan)
     */
    public function update(Request $request, $id)
    {
        try {
            $pengembalian = PengembalianBarang::with(['detailpengembalians', 'verifikasi'])->findOrFail($id);

            // ✅ Tidak bisa edit jika sudah diverifikasi
            if ($pengembalian->isVerified()) {
                toast('Pengembalian yang sudah diverifikasi PIC tidak dapat diedit.', 'error');
                return back();
            }

            $validated = $request->validate([
                'tanggal_kembali' => 'required|date',
                'keterangan'      => 'nullable|string|max:500',
                // ✅ UPDATED: Validasi status_awal bukan kondisi
                'detail_id'       => 'required|array',
                'detail_id.*'     => 'required|exists:detail_pengembalian_barangs,id',
                'status_awal'     => 'required|array',
                'status_awal.*'   => 'required|in:baik,bermasalah',
            ], [
                'required' => ':attribute harus diisi',
                'exists'   => ':attribute tidak valid',
                'in'       => ':attribute tidak valid',
                'date'     => ':attribute harus berupa tanggal',
            ]);

            DB::beginTransaction();

            // Update tanggal & keterangan
            $pengembalian->update([
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'keterangan'      => $validated['keterangan'] ?? null,
            ]);

            // ✅ Update status_awal per detail
            $hasProblematicItems = false;
            foreach ($validated['detail_id'] as $index => $detailId) {
                $statusAwal = $validated['status_awal'][$index];

                $detail = DetailPengembalianBarang::find($detailId);
                if ($detail) {
                    $detail->update(['status_awal' => $statusAwal]);
                }

                if ($statusAwal === 'bermasalah') {
                    $hasProblematicItems = true;
                }
            }

            // ✅ Update status pengembalian
            $newStatus = $hasProblematicItems ? 'menunggu_pic' : 'dikembalikan';
            $pengembalian->update(['status' => $newStatus]);

            DB::commit();

            Log::info('Pengembalian berhasil diupdate', [
                'id'                    => $pengembalian->id,
                'has_problematic_items' => $hasProblematicItems,
                'new_status'            => $newStatus,
                'updated_by'            => auth()->id(),
            ]);

            toast('Data pengembalian berhasil diperbarui.', 'success');
            return redirect()->route('backend.pengembalian.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update pengembalian error', ['message' => $e->getMessage()]);
            toast($e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * Destroy - Hapus pengembalian
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pengembalian = PengembalianBarang::with(['detailpengembalians', 'verifikasi'])->findOrFail($id);

            // ✅ Tidak bisa hapus jika sudah diverifikasi
            if ($pengembalian->isVerified()) {
                toast('Pengembalian yang sudah diverifikasi PIC tidak dapat dihapus.', 'error');
                return back();
            }

            // ✅ Jika status dikembalikan, kembalikan stok ke semula
            if ($pengembalian->status === 'dikembalikan') {
                $peminjaman = $pengembalian->peminjamanBarang;

                foreach ($peminjaman->detailbarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        if ($barangRuangan->qty >= $detail->jumlah) {
                            $barangRuangan->decrement('qty', $detail->jumlah);
                        }

                        if ($barangRuangan->qty == 0) {
                            $barangRuangan->update(['status' => 'sedang dipinjam']);
                        }
                    }
                }

                $peminjaman->update(['status' => 'disetujui']);
            }

            $pengembalian->detailpengembalians()->delete();
            $pengembalian->delete();

            DB::commit();

            toast('Pengembalian berhasil dihapus.', 'success');
            return redirect()->route('backend.pengembalian.index');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Hapus pengembalian error: ' . $e->getMessage());
            toast('Gagal menghapus pengembalian.', 'error');
            return back();
        }
    }

    /**
     * ✅ NEW: Method dipanggil setelah PIC verifikasi selesai
     * Update stok barang setelah verifikasi PIC
     */
    public function updateStatusFromVerifikasi($pengembalianId)
    {
        try {
            DB::beginTransaction();

            $pengembalian = PengembalianBarang::with(['peminjamanBarang.detailbarangs', 'verifikasi'])->findOrFail($pengembalianId);

            if (! $pengembalian->isVerified()) {
                throw new Exception('Pengembalian belum diverifikasi');
            }

            $verifikasi = $pengembalian->verifikasi;

            // ✅ Tentukan status berdasarkan kondisi verifikasi PIC
            if (in_array($verifikasi->kondisi, ['rusak_berat', 'hilang'])) {
                $status = 'perlu_tindakan'; // Admin harus ambil keputusan
            } else {
                $status = 'dikembalikan'; // Rusak ringan atau baik, bisa dikembalikan
            }

            // ✅ Kembalikan stok jika bukan rusak berat/hilang
            if ($status === 'dikembalikan') {
                $peminjaman = $pengembalian->peminjamanBarang;

                foreach ($peminjaman->detailbarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        $barangRuangan->increment('qty', $detail->jumlah);

                        if ($barangRuangan->qty > 0) {
                            $barangRuangan->update(['status' => 'tersedia']);
                        }
                    }
                }

                $peminjaman->update(['status' => 'dikembalikan']);
            }

            $pengembalian->update(['status' => $status]);

            DB::commit();

            Log::info('Status pengembalian diupdate dari verifikasi PIC', [
                'pengembalian_id' => $pengembalianId,
                'kondisi'         => $verifikasi->kondisi,
                'new_status'      => $status,
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error update status from verifikasi: ' . $e->getMessage());
            return false;
        }
    }
}
