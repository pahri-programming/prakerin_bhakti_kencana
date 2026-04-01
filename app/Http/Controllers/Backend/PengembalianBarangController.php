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

    public function export()
    {
        $data = PengembalianBarang::with([
            'peminjamanBarang.user',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic',
        ])->latest()->get()->map(function ($p) {
            $p->tanggal_kembali_format = Carbon::parse($p->tanggal_kembali)->translatedFormat('d F Y');
            return $p;
        });

        $pdf = Pdf::loadView('backend.pengembalian.pdf', compact('data'))->setPaper('A4', 'landscape');
        return $pdf->download('laporan-pengembalian-' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    public function index(Request $request)
    {
        $query = PengembalianBarang::with([
            'peminjamanBarang.user',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('peminjamanBarang', function ($q2) use ($search) {
                    $q2->where('kode', 'like', "%{$search}%")->orWhere('nama_peminjam', 'like', "%{$search}%");
                })->orWhereHas('peminjamanBarang.user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('tanggal_kembali')) {
            $query->whereDate('tanggal_kembali', $request->tanggal_kembali);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengembalian = $query->latest()->paginate(10);
        confirmDelete('Data Pengembalian Barang', 'Apakah anda yakin ingin menghapus data pengembalian ini?');
        return view('backend.pengembalian.index', compact('pengembalian'));
    }

    public function create()
    {
        $peminjamans = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan'])
            ->where('status', 'disetujui')
            ->whereDoesntHave('pengembalianbarangs')
            ->latest()
            ->get();
        return view('backend.pengembalian.create', compact('peminjamans'));
    }

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
                'status_awal'          => 'required|array|min:1',
                'status_awal.*'        => 'required|in:baik,bermasalah',
            ]);

            $peminjaman = PeminjamanBarang::with('detailbarangs.barangRuangan')->findOrFail($validated['peminjaman_barang_id']);

            if ($peminjaman->status !== 'disetujui') {
                toast('Peminjaman harus berstatus disetujui untuk dikembalikan.', 'error');
                return back()->withInput();
            }

            if ($peminjaman->hasReturn()) {
                toast('Peminjaman ini sudah memiliki data pengembalian.', 'error');
                return back()->withInput();
            }

            $detailBarangs       = [];
            $hasProblematicItems = false;

            foreach ($validated['barang_id'] as $index => $barangId) {
                $detailBarangs[] = [
                    'barang_id'   => $barangId,
                    'jumlah'      => $validated['jumlah'][$index],
                    'status_awal' => $validated['status_awal'][$index],
                ];
                if ($validated['status_awal'][$index] === 'bermasalah') {
                    $hasProblematicItems = true;
                }
            }

            DB::beginTransaction();

            $status = $hasProblematicItems ? 'menunggu_pic' : 'dikembalikan';

            $pengembalian = PengembalianBarang::create([
                'peminjaman_barang_id' => $validated['peminjaman_barang_id'],
                'barang_ruangan_id'    => $validated['barang_ruangan_id'],
                'tanggal_kembali'      => $validated['tanggal_kembali'],
                'status'               => $status,
                'keterangan'           => $validated['keterangan'] ?? null,
            ]);

            $detailCount = 0;
            foreach ($detailBarangs as $detail) {
                if (DetailPengembalianBarang::create([
                    'pengembalian_barang_id' => $pengembalian->id,
                    'barang_id'              => $detail['barang_id'],
                    'jumlah'                 => $detail['jumlah'],
                    'status_awal'            => $detail['status_awal'],
                ])) {
                    $detailCount++;
                }
            }

            // ✅ FIX 1: Kembalikan stok hanya untuk barang yang BAIK
            if (! $hasProblematicItems) {
                // Semua baik → kembalikan semua stok
                foreach ($peminjaman->detailbarangs as $detailPeminjaman) {
                    $barangRuangan = BarangRuangan::where('id', $detailPeminjaman->barang_ruangan_id)->lockForUpdate()->first();
                    if ($barangRuangan) {
                        $barangRuangan->increment('qty', $detailPeminjaman->jumlah);
                        if ($barangRuangan->qty > 0) {
                            $barangRuangan->update(['status' => 'tersedia']);
                        }
                    }
                }
                $peminjaman->update(['status' => 'dikembalikan']);
            } else {
                // Ada yang bermasalah → kembalikan stok HANYA yang BAIK
                $barangIdBaik = collect($detailBarangs)->where('status_awal', 'baik')->pluck('barang_id')->toArray();

                foreach ($peminjaman->detailbarangs as $detailPeminjaman) {
                    $barangId = $detailPeminjaman->barangRuangan->barang_id ?? null;
                    if ($barangId && in_array($barangId, $barangIdBaik)) {
                        $barangRuangan = BarangRuangan::where('id', $detailPeminjaman->barang_ruangan_id)->lockForUpdate()->first();
                        if ($barangRuangan) {
                            $barangRuangan->increment('qty', $detailPeminjaman->jumlah);
                            if ($barangRuangan->qty > 0) {
                                $barangRuangan->update(['status' => 'tersedia']);
                            }
                        }
                    }
                    // Barang bermasalah → stok ditahan, menunggu verifikasi PIC
                }
                // Status peminjaman tetap 'disetujui' sampai semua selesai
            }

            DB::commit();

            Log::info('Pengembalian barang berhasil dibuat', [
                'id'          => $pengembalian->id, 'peminjaman'       => $peminjaman->kode,
                'jumlah_item' => $detailCount, 'has_problematic_items' => $hasProblematicItems,
            ]);

            if ($hasProblematicItems) {
                toast('Pengembalian berhasil dicatat. Ada barang bermasalah, menunggu verifikasi PIC.', 'warning');
            } else {
                toast('Pengembalian berhasil dicatat. Semua barang dalam kondisi baik, stok sudah dikembalikan.', 'success');
            }

            return redirect()->route('backend.pengembalian.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat membuat pengembalian', ['message' => $e->getMessage()]);
            toast('Gagal membuat pengembalian: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang.user',
            'peminjamanBarang.detailbarangs.barangRuangan.barang',
            'peminjamanBarang.detailbarangs.barangRuangan.ruangan',
            'barangRuangan.barang',
            'barangRuangan.ruangan',
            'detailpengembalians.barang',
            'verifikasi.pic',
        ])->findOrFail($id);

        $pengembalian->tanggal_kembali_format = Carbon::parse($pengembalian->tanggal_kembali)->translatedFormat('d F Y');
        return view('backend.pengembalian.show', compact('pengembalian'));
    }

    public function edit($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang', 'barangRuangan', 'detailpengembalians', 'verifikasi',
        ])->findOrFail($id);

        if ($pengembalian->isVerified()) {
            toast('Pengembalian yang sudah diverifikasi PIC tidak dapat diedit.', 'error');
            return redirect()->route('backend.pengembalian.show', $id);
        }

        $barangs        = Barang::orderBy('nama')->get();
        $barangRuangans = BarangRuangan::with(['barang', 'ruangan'])->get();
        return view('backend.pengembalian.edit', compact('pengembalian', 'barangs', 'barangRuangans'));
    }

    public function update(Request $request, $id)
    {
        try {
            $pengembalian = PengembalianBarang::with(['detailpengembalians', 'verifikasi'])->findOrFail($id);

            if ($pengembalian->isVerified()) {
                toast('Pengembalian yang sudah diverifikasi PIC tidak dapat diedit.', 'error');
                return back();
            }

            $validated = $request->validate([
                'tanggal_kembali' => 'required|date',
                'keterangan'      => 'nullable|string|max:500',
                'detail_id'       => 'required|array',
                'detail_id.*'     => 'required|exists:detail_pengembalian_barangs,id',
                'status_awal'     => 'required|array',
                'status_awal.*'   => 'required|in:baik,bermasalah',
            ]);

            DB::beginTransaction();

            $pengembalian->update([
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'keterangan'      => $validated['keterangan'] ?? null,
            ]);

            $hasProblematicItems = false;
            foreach ($validated['detail_id'] as $index => $detailId) {
                $statusAwal = $validated['status_awal'][$index];
                $detail     = DetailPengembalianBarang::find($detailId);
                if ($detail) {
                    $detail->update(['status_awal' => $statusAwal]);
                }
                if ($statusAwal === 'bermasalah') {
                    $hasProblematicItems = true;
                }
            }

            $pengembalian->update(['status' => $hasProblematicItems ? 'menunggu_pic' : 'dikembalikan']);
            DB::commit();

            toast('Data pengembalian berhasil diperbarui.', 'success');
            return redirect()->route('backend.pengembalian.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            toast($e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pengembalian = PengembalianBarang::with(['detailpengembalians', 'verifikasi'])->findOrFail($id);

            if ($pengembalian->isVerified()) {
                toast('Pengembalian yang sudah diverifikasi PIC tidak dapat dihapus.', 'error');
                return back();
            }

            if ($pengembalian->status === 'dikembalikan') {
                $peminjaman = $pengembalian->peminjamanBarang;
                foreach ($peminjaman->detailbarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)->lockForUpdate()->first();
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
     * ✅ FIX 2: updateStatusFromVerifikasi
     * Dipanggil setelah PIC verifikasi selesai.
     * Barang BAIK sudah dikembalikan saat store() — jangan dikembalikan 2x.
     * Barang bermasalah kondisi rusak_ringan → langsung kembalikan stok.
     * Barang bermasalah kondisi rusak_berat/hilang → tahan stok, tunggu denda.
     */
    public function updateStatusFromVerifikasi($pengembalianId)
    {
        try {
            DB::beginTransaction();

            $pengembalian = PengembalianBarang::with([
                'peminjamanBarang.detailbarangs.barangRuangan',
                'detailpengembalians',
                'verifikasi',
            ])->findOrFail($pengembalianId);

            if (! $pengembalian->isVerified()) {
                throw new Exception('Pengembalian belum diverifikasi');
            }

            $kondisi = $pengembalian->verifikasi->kondisi;

            if (in_array($kondisi, ['rusak_berat', 'hilang'])) {
                // Perlu denda → stok bermasalah masih ditahan
                $pengembalian->update(['status' => 'perlu_tindakan']);

            } elseif ($kondisi === 'rusak_ringan') {
                // Rusak ringan tetap perlu keputusan admin → denda atau dibebaskan
                $pengembalian->update(['status' => 'perlu_tindakan']);
                DB::commit();
                return true;
            } else {
                // Kondisi baik → stok sudah dikembalikan saat store()
                $pengembalian->update(['status' => 'dikembalikan']);
                $pengembalian->peminjamanBarang->update(['status' => 'dikembalikan']);
            }

            DB::commit();
            return true;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updateStatusFromVerifikasi: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ FIX 3: kembalikanStokBermasalah (method baru)
     * Dipanggil dari DendaPengembalianController setelah denda selesai,
     * dan dari updateStatusFromVerifikasi untuk kasus rusak_ringan.
     * Barang HILANG → stok tidak pernah kembali.
     */
    public function kembalikanStokBermasalah($pengembalianId)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang.detailbarangs.barangRuangan',
            'detailpengembalians',
            'verifikasi',
        ])->findOrFail($pengembalianId);

        $kondisiVerifikasi = $pengembalian->verifikasi?->kondisi;

        if ($kondisiVerifikasi === 'hilang') {
            // Barang hilang → stok tidak kembali, tapi status tetap selesai
            Log::info('Barang hilang, stok tidak dikembalikan', ['pengembalian_id' => $pengembalianId]);
        } else {
            // Barang rusak (ringan/berat) → kembalikan stok yang bermasalah
            $barangIdBermasalah = $pengembalian->detailpengembalians
                ->where('status_awal', 'bermasalah')
                ->pluck('barang_id')
                ->toArray();

            foreach ($pengembalian->peminjamanBarang->detailbarangs as $detailPeminjaman) {
                $barangId = $detailPeminjaman->barangRuangan->barang_id ?? null;

                if ($barangId && in_array($barangId, $barangIdBermasalah)) {
                    $barangRuangan = BarangRuangan::where('id', $detailPeminjaman->barang_ruangan_id)
                        ->lockForUpdate()->first();

                    if ($barangRuangan) {
                        $barangRuangan->increment('qty', $detailPeminjaman->jumlah);
                        if ($barangRuangan->qty > 0) {
                            $barangRuangan->update(['status' => 'tersedia']);
                        }
                        Log::info('Stok bermasalah dikembalikan', [
                            'barang_ruangan_id' => $barangRuangan->id,
                            'kondisi'           => $kondisiVerifikasi,
                            'jumlah'            => $detailPeminjaman->jumlah,
                        ]);
                    }
                }
            }
        }

        // Update status jadi selesai
        $pengembalian->update(['status' => 'dikembalikan']);
        $pengembalian->peminjamanBarang->update(['status' => 'dikembalikan']);
    }
}
