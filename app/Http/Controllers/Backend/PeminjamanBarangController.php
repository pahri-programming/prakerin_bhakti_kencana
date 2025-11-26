<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PeminjamanBarang;
use App\Models\User;
use App\Services\AvailabilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeminjamanBarangController extends Controller
{
    protected $avail;

    public function __construct(AvailabilityService $avail)
    {
        $this->middleware('auth');
        $this->avail = $avail;
    }

    /**
     * Export laporan
     */
    public function export()
    {
        $data = PeminjamanBarang::with(['user', 'barang'])
            ->latest()
            ->get()
            ->map(function ($p) {
                $p->tanggal_pinjam_format  = Carbon::parse($p->tanggal_pinjam)->translatedFormat('d F Y');
                $p->tanggal_kembali_format = Carbon::parse($p->tanggal_kembali)->translatedFormat('d F Y');
                return $p;
            });

        $pdf = Pdf::loadView('backend.peminjaman.pinjampdf', compact('data'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Index
     */
    public function index(Request $request)
    {
        // AUTO-CHECK: ubah status jadi 'selesai' jika end_datetime < now()
        $now = now()->toDateTimeString();

        // Gunakan TIMESTAMP(tanggal_kembali, waktu_selesai) < now
        $expired = PeminjamanBarang::whereNotIn('status', ['selesai', 'dikembalikan', 'ditolak'])
            ->whereRaw("TIMESTAMP(tanggal_kembali, waktu_selesai) < ?", [$now])
            ->get();

        foreach ($expired as $p) {
            DB::transaction(function () use ($p) {
                $oldStatus = $p->status;

                // Kembalikan stok HANYA jika status sebelumnya disetujui/dipinjam
                if (in_array($oldStatus, ['disetujui', 'dipinjam'])) {
                    $barang = Barang::where('id', $p->barang_id)->lockForUpdate()->first();
                    if ($barang) {
                        $barang->increment('stok', $p->jumlah);
                    }
                }

                // ubah status
                $p->update(['status' => 'selesai']);

                // broadcast
                event(new \App\Events\PeminjamanExpired($p));
                event(new \App\Events\PeminjamanStatusChanged($p, $oldStatus));
            });
        }

        // FILTER & QUERY
        $query = PeminjamanBarang::with(['user', 'barang']);

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->filled('tanggal_pinjam')) {
            $query->whereDate('tanggal_pinjam', $request->tanggal_pinjam);
        }

        if ($request->filled('tanggal_kembali')) {
            $query->whereDate('tanggal_kembali', $request->tanggal_kembali);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        //confirm delete
        $title = 'Data Peminjaman Barang';
        $text  = "Apakah anda yakin ingin menghapus data peminjaman barang ini?";
        confirmDelete($title, $text);

        $peminjaman = $query->latest()->get();
        $barangs    = Barang::orderBy('nama')->get();

        

        return view('backend.peminjaman.index', compact('peminjaman', 'barangs'));
    }

    public function create()
    {
        $barangs = Barang::all();
        $users   = User::all();
        return view('backend.peminjaman.create', compact('barangs', 'users'));
    }

    /**
     * Store new peminjaman (multi-day)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'         => 'required|exists:users,id',
            'barang_id'       => 'required|exists:barangs,id',
            'jumlah'          => 'required|integer|min:1',
            'tanggal_pinjam'  => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i',
            'keterangan'      => 'nullable|string|max:255',
        ], [
            'required'    => ':attribute harus diisi',
            'exists'      => ':attribute tidak valid',
            'integer'     => ':attribute harus berupa angka',
            'min'         => ':attribute minimal :min',
            'date'        => ':attribute harus berupa tanggal',
            'date_format' => ':attribute harus berupa format waktu yang valid',
        ]);

        // Build start/end datetime
        $start = Carbon::parse("{$validated['tanggal_pinjam']} {$validated['waktu_mulai']}");
        $end   = Carbon::parse("{$validated['tanggal_kembali']} {$validated['waktu_selesai']}");

        // Validasi waktu
        if ($start->lt(now())) {
            toast('Waktu mulai sudah lewat. Silakan pilih waktu yang valid.', 'error');
            return back()->withInput();
        }
        if ($end->lte($start)) {
            toast('Waktu kembali harus setelah waktu pinjam.', 'error');
            return back()->withInput();
        }

        $barang = Barang::findOrFail($validated['barang_id']);

        // Check availability (pass tanggal_pinjam, tanggal_kembali, waktu)
        $cek = $this->avail->check(
            $validated['barang_id'],
            $validated['tanggal_pinjam'],
            $validated['tanggal_kembali'],
            $validated['waktu_mulai'],
            $validated['waktu_selesai']
        );

        if (! $cek['status']) {
            toast($cek['message'], 'error');
            return back()->withInput();
        }

        if ($validated['jumlah'] > $cek['available']) {
            toast("Hanya tersedia {$cek['available']} unit.", 'error');
            return back()->withInput();
        }

        try {
            $peminjaman = PeminjamanBarang::create([
                'user_id'         => $validated['user_id'],
                'barang_id'       => $validated['barang_id'],
                'jumlah'          => (int) $validated['jumlah'],
                'tanggal_pinjam'  => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'waktu_mulai'     => $validated['waktu_mulai'],
                'waktu_selesai'   => $validated['waktu_selesai'],
                'keterangan'      => $validated['keterangan'] ?? '-',
                'status'          => 'menunggu',
            ]);

            Log::info('Peminjaman baru dibuat', [
                'id'     => $peminjaman->id,
                'barang' => $barang->nama,
            ]);

            toast('Peminjaman berhasil Dibuat.', 'success');
            return redirect()->route('backend.peminjaman.index');

        } catch (Exception $e) {
            Log::error('Gagal membuat peminjaman: ' . $e->getMessage());
            toast('Gagal membuat peminjaman. Silakan coba lagi.', 'error');
            return back()->withInput();
        }
    }

    /**
     * Show
     */
    public function show($id)
    {
        $peminjaman = PeminjamanBarang::with(['user', 'barang'])->findOrFail($id);

        // prepare formatted fields for view
        $peminjaman->tanggal_pinjam_format  = Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y');
        $peminjaman->tanggal_kembali_format = Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y');
        $peminjaman->waktu_range            = "{$peminjaman->waktu_mulai} - {$peminjaman->waktu_selesai}";

        return view('backend.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Edit
     */
    public function edit($id)
    {
        $peminjaman = PeminjamanBarang::findOrFail($id);
        $barangs    = Barang::all();
        $users      = User::all();
        return view('backend.peminjaman.edit', compact('peminjaman', 'barangs', 'users'));
    }

    /**
     * Update with safe stock handling (transaction + lock)
     */

    public function update(Request $request, $id)
    {
        $peminjaman = PeminjamanBarang::findOrFail($id);

        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'barang_id'       => 'required|exists:barangs,id',
            'jumlah'          => 'required|integer|min:1',
            'tanggal_pinjam'  => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i',
            'status'          => 'required|in:menunggu,disetujui,ditolak,dipinjam,dikembalikan,selesai',
            'deskripsi'       => 'nullable|string|max:500',
            'keterangan'      => 'nullable|string|max:2000',
        ]);

        $oldStatus = $peminjaman->status;
        $newStatus = $request->status;

        // Validate overall datetime ordering
        $start = Carbon::parse("{$request->tanggal_pinjam} {$request->waktu_mulai}");
        $end   = Carbon::parse("{$request->tanggal_kembali} {$request->waktu_selesai}");
        if ($start->gte($end)) {
            toast('Waktu kembali harus setelah waktu pinjam.', 'error');
            return back()->withInput();
        }

        try {
            DB::transaction(function () use ($request, $peminjaman, $oldStatus, $newStatus) {
                $oldBarangId = $peminjaman->barang_id;
                $oldJumlah   = (int) $peminjaman->jumlah;

                $newBarangId = (int) $request->barang_id;
                $newJumlah   = (int) $request->jumlah;

                // Flag apakah status "reservasi" yang mengurangi stok
                $isOldReserving = in_array($oldStatus, ['disetujui', 'dipinjam']);
                $isNewReserving = in_array($newStatus, ['disetujui', 'dipinjam']);

                // 1) HANDLE switching antara barang yang berbeda saat keduanya reserving:
                //    kembalikan stok ke barang lama dulu (unlock old), lalu reserve barang baru.
                if ($isOldReserving && $isNewReserving && $oldBarangId !== $newBarangId) {
                    // kembalikan stok ke old barang (lock row)
                    $oldBarang = Barang::where('id', $oldBarangId)->lockForUpdate()->first();
                    if ($oldBarang) {
                        $oldBarang->increment('stok', $oldJumlah);
                    }
                }

                // 2) HANDLE reserving pada new barang (decrement stok sesuai net logic)
                if ($isNewReserving) {
                    $newBarang = Barang::where('id', $newBarangId)->lockForUpdate()->first();
                    if (! $newBarang) {
                        throw new ModelNotFoundException("Barang tidak ditemukan.");
                    }

                    if ($oldBarangId === $newBarangId && $isOldReserving) {
                        // same barang and previously reserving -> apply net change
                        $net = $newJumlah - $oldJumlah;
                        if ($net > 0) {
                            if ($newBarang->stok < $net) {
                                throw new Exception("Stok {$newBarang->nama} tidak mencukupi. Tersedia: {$newBarang->stok}, dibutuhkan tambahan: {$net}");
                            }
                            $newBarang->decrement('stok', $net);
                        } elseif ($net < 0) {
                            $newBarang->increment('stok', abs($net));
                        }
                    } else {
                        // barang berbeda OR old tidak reserving -> full reserve
                        if ($newBarang->stok < $newJumlah) {
                            throw new Exception("Stok {$newBarang->nama} tidak mencukupi. Tersedia: {$newBarang->stok}, diminta: {$newJumlah}");
                        }
                        $newBarang->decrement('stok', $newJumlah);
                    }
                }

                // 3) HANDLE kasus where old was reserving but new is NOT reserving:
                //    (return stock for the old barang) — DO THIS ONLY ONCE.
                if ($isOldReserving && ! $isNewReserving) {
                    // gunakan StockService agar logic stok terpusat (bila ada auditing / logs di service)
                    $stock = new \App\Services\StockService();

                    // jika barang diganti sebelumnya pada langkah (1) kita sudah mengembalikan old barang
                    // hanya lakukan increase jika oldBarangId == newBarangId OR kita belum mengembalikan old
                    if (! ($isNewReserving && $oldBarangId !== $newBarangId && $isOldReserving)) {
                        // safe increase
                        $stock->increase($oldBarangId, $oldJumlah);
                    } else {
                        // pada path switching yang sudah menangani increment langsung di DB (step 1),
                        // jangan panggil StockService lagi (menghindari double-return).
                    }
                }

                // 4) akhirnya update model peminjaman
                $peminjaman->update([
                    'user_id'         => $request->user_id,
                    'barang_id'       => $request->barang_id,
                    'jumlah'          => $request->jumlah,
                    'tanggal_pinjam'  => $request->tanggal_pinjam,
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'waktu_mulai'     => $request->waktu_mulai,
                    'waktu_selesai'   => $request->waktu_selesai,
                    'status'          => $request->status,
                    'deskripsi'       => $request->deskripsi ?? null,
                    'keterangan'      => $request->keterangan ?? '-',
                ]);
            }, 5);

            // broadcast/notify jika status berubah
            if ($peminjaman->wasChanged('status')) {
                event(new \App\Events\PeminjamanStatusChanged($peminjaman, $oldStatus));
            }

            toast('Data peminjaman berhasil diperbarui.', 'success');
            return redirect()->route('backend.peminjaman.index');

        } catch (ModelNotFoundException $e) {
            toast($e->getMessage(), 'error');
            return back()->withInput();
        } catch (Exception $e) {
            // transaksi otomatis rollback saat exception
            Log::error('Update peminjaman error: ' . $e->getMessage());
            toast($e->getMessage(), 'error');
            return back()->withInput();
        }
    }   

    /**
     * Destroy
     */
    public function destroy($id)
    {
        $p = PeminjamanBarang::findOrFail($id);
        // Jika ingin restore stok saat menghapus record yang sedang disetujui/dipinjam:
        if (in_array($p->status, ['disetujui', 'dipinjam'])) {
            DB::transaction(function () use ($p) {
                $barang = Barang::where('id', $p->barang_id)->lockForUpdate()->first();
                if ($barang) {
                    $barang->increment('stok', $p->jumlah);
                }
                $p->delete();
            });
        } else {
            $p->delete();
        }

        toast('Peminjaman dihapus', 'success');
        return back();
    }
}
