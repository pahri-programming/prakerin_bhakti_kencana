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
            'detailpengembalians',
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

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengembalian = $query->latest()->paginate(10);

        // Confirm delete
        $title = 'Data Pengembalian Barang';
        $text  = "Apakah anda yakin ingin menghapus data pengembalian ini?";
        confirmDelete($title, $text);

        return view('backend.pengembalian.index', compact('pengembalian'));
    }

    /**
     * Create - Form tambah pengembalian
     */
    // Di PengembalianBarangController.php
    public function create()
    {
        $peminjamans = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan'])
            ->where('status', 'disetujui')
            ->whereDoesntHave('pengembalianbarangs') // ← Ubah ini
            ->latest()
            ->get();

        return view('backend.pengembalian.create', compact('peminjamans'));
    }
    /**
     * Store - Simpan pengembalian baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'peminjaman_barang_id' => 'required|exists:peminjaman_barangs,id',
                'barang_ruangan_id'    => 'required|exists:barang_ruangans,id',
                'tanggal_kembali'      => 'required|date',
                'status'               => 'required|in:dikembalikan,belum dikembalikan',
                'keterangan'           => 'nullable|string|max:500',
                'barang_id'            => 'required|array|min:1',
                'barang_id.*'          => 'required|exists:barangs,id',
                'jumlah'               => 'required|array|min:1',
                'jumlah.*'             => 'required|integer|min:1',
                'kondisi'              => 'required|array|min:1',
                'kondisi.*'            => 'required|in:baik,rusak,hilang',
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
            $detailBarangs = [];
            foreach ($validated['barang_id'] as $index => $barangId) {
                $jumlah  = $validated['jumlah'][$index];
                $kondisi = $validated['kondisi'][$index];

                $detailBarangs[] = [
                    'barang_id' => $barangId,
                    'jumlah'    => $jumlah,
                    'kondisi'   => $kondisi,
                ];
            }

            DB::beginTransaction();

            // Create pengembalian
            $pengembalian = PengembalianBarang::create([
                'peminjaman_barang_id' => $validated['peminjaman_barang_id'],
                'barang_ruangan_id'    => $validated['barang_ruangan_id'],
                'tanggal_kembali'      => $validated['tanggal_kembali'],
                'status'               => $validated['status'],
                'keterangan'           => $validated['keterangan'] ?? null,
            ]);

            // Create detail pengembalian
            $detailCount = 0;
            foreach ($detailBarangs as $detail) {
                $detailPengembalian = DetailPengembalianBarang::create([
                    'pengembalian_barang_id' => $pengembalian->id,
                    'barang_id'              => $detail['barang_id'],
                    'jumlah'                 => $detail['jumlah'],
                    'kondisi'                => $detail['kondisi'],
                ]);

                if ($detailPengembalian) {
                    $detailCount++;
                }
            }

            // Jika status dikembalikan, kembalikan qty ke barang ruangan
            if ($validated['status'] === 'dikembalikan') {
                foreach ($peminjaman->detailbarangs as $detailPeminjaman) {
                    $barangRuangan = BarangRuangan::where('id', $detailPeminjaman->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        // Tambah qty
                        $barangRuangan->increment('qty', $detailPeminjaman->jumlah);

                        // Ubah status jadi 'tersedia' jika qty > 0
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
                'id'          => $pengembalian->id,
                'peminjaman'  => $peminjaman->kode,
                'jumlah_item' => $detailCount,
            ]);

            toast(
                'Pengembalian berhasil dibuat. Total ' . $detailCount . ' item barang.',
                'success'
            );

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
        ])->findOrFail($id);

        // Format tanggal
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
        ])->findOrFail($id);

        $barangs        = Barang::orderBy('nama')->get();
        $barangRuangans = BarangRuangan::with(['barang', 'ruangan'])->get();

        return view('backend.pengembalian.edit', compact('pengembalian', 'barangs', 'barangRuangans'));
    }

    /**
     * Update - Update pengembalian
     */
    public function update(Request $request, $id)
    {
        try {
            $pengembalian = PengembalianBarang::with(['detailpengembalians'])->findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'barang_ruangan_id' => 'required|exists:barang_ruangans,id',
                'tanggal_kembali'   => 'required|date',
                'status'            => 'required|in:dikembalikan,belum dikembalikan',
                'keterangan'        => 'nullable|string|max:500',
                // Validasi untuk update kondisi
                'detail_id'         => 'required|array',
                'detail_id.*'       => 'required|exists:detail_pengembalian_barangs,id',
                'kondisi'           => 'required|array',
                'kondisi.*'         => 'required|in:baik,rusak,hilang',
            ], [
                'required' => ':attribute harus diisi',
                'exists'   => ':attribute tidak valid',
                'in'       => ':attribute tidak valid',
                'date'     => ':attribute harus berupa tanggal',
            ]);

            $oldStatus = $pengembalian->status;
            $newStatus = $validated['status'];

            DB::beginTransaction();

            // Update pengembalian
            $pengembalian->update([
                'barang_ruangan_id' => $validated['barang_ruangan_id'],
                'tanggal_kembali'   => $validated['tanggal_kembali'],
                'status'            => $validated['status'],
                'keterangan'        => $validated['keterangan'] ?? null,
            ]);

            // Update kondisi barang pada detail pengembalian
            foreach ($validated['detail_id'] as $index => $detailId) {
                $kondisi = $validated['kondisi'][$index];

                $detail = DetailPengembalianBarang::find($detailId);
                if ($detail) {
                    $detail->update(['kondisi' => $kondisi]);
                }
            }

            // Handle stock changes based on status
            if ($oldStatus !== $newStatus) {
                $peminjaman = $pengembalian->peminjamanBarang;

                // Jika status berubah ke dikembalikan, kembalikan qty
                if ($oldStatus === 'belum dikembalikan' && $newStatus === 'dikembalikan') {
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

                    // Update status peminjaman
                    $peminjaman->update(['status' => 'dikembalikan']);
                }

                // Jika status berubah ke belum dikembalikan, kurangi qty kembali
                if ($oldStatus === 'dikembalikan' && $newStatus === 'belum dikembalikan') {
                    foreach ($peminjaman->detailbarangs as $detail) {
                        $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                            ->lockForUpdate()
                            ->first();

                        if ($barangRuangan) {
                            if ($barangRuangan->qty < $detail->jumlah) {
                                throw new Exception("Qty tidak mencukupi untuk dibatalkan.");
                            }

                            $barangRuangan->decrement('qty', $detail->jumlah);

                            if ($barangRuangan->qty == 0) {
                                $barangRuangan->update(['status' => 'sedang dipinjam']);
                            }
                        }
                    }

                    // Update status peminjaman kembali ke disetujui
                    $peminjaman->update(['status' => 'disetujui']);
                }
            }

            DB::commit();

            Log::info('Pengembalian berhasil diupdate', [
                'id'         => $pengembalian->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            toast('Data pengembalian berhasil diperbarui.', 'success');
            return redirect()->route('backend.pengembalian.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validasi gagal saat update pengembalian', [
                'errors' => $e->errors(),
            ]);
            throw $e;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Update pengembalian error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

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

            $pengembalian = PengembalianBarang::with('detailpengembalians')->findOrFail($id);

            // Jika status dikembalikan, kembalikan qty ke kondisi semula (kurangi lagi)
            if ($pengembalian->status === 'dikembalikan') {
                $peminjaman = $pengembalian->peminjamanBarang;

                foreach ($peminjaman->detailbarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        // Kurangi qty (karena sebelumnya sudah ditambah saat pengembalian)
                        if ($barangRuangan->qty >= $detail->jumlah) {
                            $barangRuangan->decrement('qty', $detail->jumlah);
                        }

                        if ($barangRuangan->qty == 0) {
                            $barangRuangan->update(['status' => 'sedang dipinjam']);
                        }
                    }
                }

                // Kembalikan status peminjaman ke disetujui
                $peminjaman->update(['status' => 'disetujui']);
            }

            // Hapus detail terlebih dahulu
            $pengembalian->detailpengembalians()->delete();

            // Hapus pengembalian
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
     * Update status pengembalian
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:dikembalikan,belum dikembalikan',
        ]);

        try {
            DB::beginTransaction();

            $pengembalian = PengembalianBarang::with('peminjamanBarang.detailbarangs')->findOrFail($id);
            $oldStatus    = $pengembalian->status;
            $newStatus    = $validated['status'];

            if ($oldStatus !== $newStatus) {
                $peminjaman = $pengembalian->peminjamanBarang;

                // Jika status berubah ke dikembalikan, kembalikan qty
                if ($oldStatus === 'belum dikembalikan' && $newStatus === 'dikembalikan') {
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

                // Jika status berubah ke belum dikembalikan, kurangi qty
                if ($oldStatus === 'dikembalikan' && $newStatus === 'belum dikembalikan') {
                    foreach ($peminjaman->detailbarangs as $detail) {
                        $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                            ->lockForUpdate()
                            ->first();

                        if ($barangRuangan) {
                            if ($barangRuangan->qty < $detail->jumlah) {
                                throw new Exception("Qty tidak mencukupi.");
                            }

                            $barangRuangan->decrement('qty', $detail->jumlah);

                            if ($barangRuangan->qty == 0) {
                                $barangRuangan->update(['status' => 'sedang dipinjam']);
                            }
                        }
                    }

                    $peminjaman->update(['status' => 'disetujui']);
                }
            }

            $pengembalian->update(['status' => $newStatus]);

            DB::commit();

            toast('Status pengembalian berhasil diperbarui.', 'success');
            return redirect()->back();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update status error: ' . $e->getMessage());
            toast($e->getMessage(), 'error');
            return back();
        }
    }
}
