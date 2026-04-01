<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DendaPengembalian;
use App\Models\PengembalianBarang;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DendaPengembalianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * INDEX: List pengembalian yang perlu tindak lanjut
     */
    public function index(Request $request)
    {
        $query = PengembalianBarang::with([
            'peminjamanBarang.user',
            'detailpengembalians.barang',
            'verifikasi.pic',
            'verifikasi.denda.admin',
            'denda',
        ])
            ->whereHas('verifikasi', function ($q) {
                $q->whereIn('status_verifikasi', ['pending', 'perlu_tindakan'])
                    ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang']);
            });

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

        if ($request->filled('kondisi')) {
            $query->whereHas('verifikasi', function ($q) use ($request) {
                $q->where('kondisi', $request->kondisi);
            });
        }

        if ($request->filled('status_denda')) {
            if ($request->status_denda === 'belum_ada') {
                $query->doesntHave('denda');
            } elseif ($request->status_denda === 'sudah_ada') {
                $query->has('denda');
            }
        }

        $pengembalian = $query->latest()->paginate(10);

        return view('backend.denda.index', compact('pengembalian'));
    }

    /**
     * SHOW: Form tindak lanjut & input denda
     */
    public function tindakLanjut($id)
    {
        $pengembalian = PengembalianBarang::with([
            'peminjamanBarang.user',
            'peminjamanBarang.detailbarangs.barangRuangan.barang',
            'detailpengembalians.barang',
            'verifikasi.pic',
            'verifikasi.denda',
            'denda.admin',
        ])->findOrFail($id);

        if (! $pengembalian->verifikasi) {
            toast('Pengembalian ini belum diverifikasi oleh PIC.', 'error');
            return redirect()->route('backend.denda.index');
        }

        $dendaSuggestion = $this->calculateDendaOtomatis($pengembalian);

        return view('backend.denda.tindak-lanjut', compact('pengembalian', 'dendaSuggestion'));
    }

    /**
     * STORE: Simpan denda & tindakan admin
     */
    public function store(Request $request, $id)
    {
        try {
            $pengembalian = PengembalianBarang::with(['verifikasi', 'denda'])
                ->findOrFail($id);

            if (! $pengembalian->verifikasi) {
                toast('Pengembalian ini belum diverifikasi oleh PIC.', 'error');
                return back();
            }

            if ($pengembalian->hasDenda()) {
                toast('Denda sudah pernah dibuat untuk pengembalian ini.', 'error');
                return back();
            }

            $validated = $request->validate([
                'jumlah_denda'        => 'required|numeric|min:0',
                'tipe_perhitungan'    => 'required|in:manual,otomatis',
                'keterangan_denda'    => 'nullable|string|max:1000',
                'tindakan_admin'      => 'required|string|max:1000',
                'status_pembayaran'   => 'required|in:belum_bayar,dibebaskan',
                'rincian_perhitungan' => 'nullable|json',
            ]);

            DB::beginTransaction();

            $denda = DendaPengembalian::create([
                'pengembalian_barang_id'     => $pengembalian->id,
                'verifikasi_pengembalian_id' => $pengembalian->verifikasi->id,
                'jumlah_denda'               => $validated['jumlah_denda'],
                'tipe_perhitungan'           => $validated['tipe_perhitungan'],
                'rincian_perhitungan'        => $validated['rincian_perhitungan'] ?? null,
                'status_pembayaran'          => $validated['status_pembayaran'],
                'keterangan_denda'           => $validated['keterangan_denda'] ?? null,
                'tindakan_admin'             => $validated['tindakan_admin'],
                'tanggal_tindakan'           => now(),
                'admin_id'                   => auth()->id(),
            ]);

            $pengembalian->verifikasi->update([
                'status_verifikasi' => 'perlu_tindakan',
                'tindakan_admin'    => $validated['tindakan_admin'],
            ]);

            if ($validated['status_pembayaran'] === 'dibebaskan') {
                DB::commit();
                app(PengembalianBarangController::class)
                    ->kembalikanStokBermasalah($pengembalian->id);

                toast('Tindak lanjut berhasil disimpan. Denda dibebaskan. Stok barang sudah dikembalikan.', 'success');
            } else {
                $pengembalian->update(['status' => 'perlu_tindakan']);
                DB::commit();

                toast('Tindak lanjut berhasil disimpan. Denda: ' . $denda->jumlah_denda_format, 'success');
            }

            return redirect()->route('backend.denda.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error store denda: ' . $e->getMessage());
            toast('Gagal menyimpan denda: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * APPROVE BUKTI: Admin terima bukti bayar dari user → status sudah_bayar
     */
    public function approveBukti(Request $request, $id) // ← method baru
    {
        try {
            $denda = DendaPengembalian::with(['pengembalianBarang'])
                ->findOrFail($id);

            if ($denda->status_pembayaran !== 'menunggu_verifikasi') {
                toast('Status denda tidak valid untuk diapprove.', 'error');
                return back();
            }

            DB::beginTransaction();

            $denda->update([
                'status_pembayaran'    => 'sudah_bayar',
                'verifikator_bayar_id' => auth()->id(),
            ]);

            DB::commit();

            Log::info('Bukti bayar denda di-approve admin', [
                'denda_id' => $denda->id,
                'admin_id' => auth()->id(),
            ]);

            // Kembalikan stok setelah pembayaran dikonfirmasi
            app(PengembalianBarangController::class)
                ->kembalikanStokBermasalah($denda->pengembalian_barang_id);

            toast('Pembayaran denda berhasil dikonfirmasi. Stok barang sudah dikembalikan.', 'success');
            return back();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error approve bukti: ' . $e->getMessage());
            toast('Gagal konfirmasi pembayaran: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * REJECT BUKTI: Admin tolak bukti bayar → status kembali belum_bayar
     */
    public function rejectBukti(Request $request, $id) // ← method baru
    {
        try {
            $denda = DendaPengembalian::findOrFail($id);

            if ($denda->status_pembayaran !== 'menunggu_verifikasi') {
                toast('Status denda tidak valid untuk ditolak.', 'error');
                return back();
            }

            $request->validate([
                'alasan_tolak' => 'required|string|max:500',
            ], [
                'alasan_tolak.required' => 'Alasan penolakan harus diisi.',
            ]);

            DB::beginTransaction();

            // Hapus bukti lama
            if ($denda->bukti_pembayaran) {
                Storage::disk('public')->delete($denda->bukti_pembayaran);
            }

            $denda->update([
                'status_pembayaran'     => 'belum_bayar',
                'bukti_pembayaran'      => null,
                'tanggal_bayar'         => null,
                'keterangan_pembayaran' => null,
                'tindakan_admin'        => 'Bukti ditolak: ' . $request->alasan_tolak,
            ]);

            DB::commit();

            Log::info('Bukti bayar denda ditolak admin', [
                'denda_id' => $denda->id,
                'alasan'   => $request->alasan_tolak,
                'admin_id' => auth()->id(),
            ]);

            toast('Bukti pembayaran ditolak. User perlu upload ulang.', 'warning');
            return back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error reject bukti: ' . $e->getMessage());
            toast('Gagal menolak bukti: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * LIST DENDA: Semua denda yang pernah dibuat
     */
    public function listDenda(Request $request)
    {
        $query = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang.user',
            'verifikasiPengembalian.pic',
            'admin',
            'verifikatorBayar',
        ]);

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pengembalianBarang.peminjamanBarang', function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama_peminjam', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tanggal_tindakan')) {
            $query->whereDate('tanggal_tindakan', $request->tanggal_tindakan);
        }

        $denda = $query->latest()->paginate(10);

        $stats = [
            'total_denda'         => DendaPengembalian::sum('jumlah_denda'),
            'belum_bayar'         => DendaPengembalian::belumBayar()->sum('jumlah_denda'),
            'menunggu_verifikasi' => DendaPengembalian::menungguVerifikasi()->count(), // ← tambah
            'sudah_bayar'         => DendaPengembalian::sudahBayar()->sum('jumlah_denda'),
            'total_transaksi'     => DendaPengembalian::count(),
        ];

        return view('backend.denda.list', compact('denda', 'stats'));
    }

    /**
     * HELPER: Calculate denda otomatis
     */
    private function calculateDendaOtomatis($pengembalian)
    {
        $verifikasi = $pengembalian->verifikasi;
        $kondisi    = $verifikasi->kondisi;

        $persentase = [
            'rusak_ringan' => 20,
            'rusak_berat'  => 80,
            'hilang'       => 100,
        ][$kondisi] ?? 0;

        $totalDenda = 0;
        $rincian    = [];

        foreach ($pengembalian->detailpengembalians as $detail) {
            if ($detail->status_awal === 'bermasalah') {
                $barang       = $detail->barang;
                $hargaSatuan  = $barang->harga ?? 0;
                $jumlah       = $detail->jumlah;
                $dendaBarang  = ($hargaSatuan * $persentase / 100) * $jumlah;
                $totalDenda  += $dendaBarang;

                $rincian[] = [
                    'barang_id'    => $barang->id,
                    'nama_barang'  => $barang->nama,
                    'harga_satuan' => $hargaSatuan,
                    'jumlah'       => $jumlah,
                    'persentase'   => $persentase,
                    'denda'        => $dendaBarang,
                ];
            }
        }

        return [
            'total'      => $totalDenda,
            'persentase' => $persentase,
            'kondisi'    => $kondisi,
            'rincian'    => $rincian,
        ];
    }
}
