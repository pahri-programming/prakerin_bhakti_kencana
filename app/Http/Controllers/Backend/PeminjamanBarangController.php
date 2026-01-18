<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangRuangan;
use App\Models\DetailPeminjamanBarang;
use App\Models\PeminjamanBarang;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeminjamanBarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Export laporan
     */
    public function export()
    {
        $data = PeminjamanBarang::with(['user', 'detailbarangs.barang'])
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
        // FILTER & QUERY
        $query = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan']);

        // Filter by search (nama peminjam atau instansi)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('instansi', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
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

        // Confirm delete
        $title = 'Data Peminjaman Barang';
        $text  = "Apakah anda yakin ingin menghapus data peminjaman barang ini?";
        confirmDelete($title, $text);

        $peminjaman = $query->latest()->paginate(10);
        $barangs    = Barang::orderBy('nama')->get();

        return view('backend.peminjaman.index', compact('peminjaman', 'barangs'));
    }

    public function create()
    {
        $barangs        = Barang::all();
        $users          = User::all();
        $barangRuangans = BarangRuangan::with(['barang', 'ruangan'])
            ->where('status', 'tersedia')
            ->where('qty', '>', 0)
            ->get();

        return view('backend.peminjaman.create', compact('barangs', 'users', 'barangRuangans'));
    }

    /**
     * Store new peminjaman (multi barang dari ruangan)
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'user_id'             => 'required|exists:users,id',
                'barang_ruangan_id'   => 'required|array|min:1',
                'barang_ruangan_id.*' => 'required|exists:barang_ruangans,id',
                'nama_peminjam'       => 'nullable|string|max:100',
                'instansi'            => 'nullable|string|max:100',
                'jumlah'              => 'required|array|min:1',
                'jumlah.*'            => 'required|integer|min:1',
                'tanggal_pinjam'      => 'required|date|after_or_equal:today',
                'tanggal_kembali'     => 'required|date|after_or_equal:tanggal_pinjam',
                'keterangan'          => 'nullable|string|max:500',
            ], [
                'required'       => ':attribute harus diisi',
                'exists'         => ':attribute tidak valid',
                'integer'        => ':attribute harus berupa angka',
                'min'            => ':attribute minimal :min',
                'array'          => ':attribute harus berupa array',
                'date'           => ':attribute harus berupa tanggal',
                'after_or_equal' => ':attribute harus setelah atau sama dengan :date',
            ]);

            // Validasi ketersediaan untuk setiap barang ruangan
            $detailBarangs = [];

            foreach ($validated['barang_ruangan_id'] as $index => $barangRuanganId) {
                $jumlah = $validated['jumlah'][$index];

                $barangRuangan = BarangRuangan::with(['barang', 'ruangan'])
                    ->find($barangRuanganId);

                // Cek apakah barang ruangan ditemukan
                if (! $barangRuangan) {
                    toast('Barang ruangan tidak ditemukan.', 'error');
                    return back()->withInput();
                }

                // Cek status ketersediaan
                if ($barangRuangan->status !== 'tersedia') {
                    toast(
                        "{$barangRuangan->barang->nama} di {$barangRuangan->ruangan->nama_ruangan}: Sedang dipinjam.",
                        'error'
                    );
                    return back()->withInput();
                }

                // Cek qty/stok
                if ($jumlah > $barangRuangan->qty) {
                    toast(
                        "{$barangRuangan->barang->nama} di {$barangRuangan->ruangan->nama_ruangan}: Qty tidak mencukupi. Tersedia {$barangRuangan->qty} unit.",
                        'error'
                    );
                    return back()->withInput();
                }

                // Tambahkan ke array untuk disimpan
                $detailBarangs[] = [
                    'barang_ruangan_id' => $barangRuanganId,
                    'jumlah'            => $jumlah,
                ];
            }

            // Mulai transaction
            DB::beginTransaction();

            // Create peminjaman
            $peminjaman = PeminjamanBarang::create([
                'user_id'         => $validated['user_id'],
                'nama_peminjam'   => $validated['nama_peminjam'] ?? null,
                'instansi'        => $validated['instansi'] ?? null,
                'tanggal_pinjam'  => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'keterangan'      => $validated['keterangan'] ?? null,
                'status'          => 'menunggu',
            ]);

            // Validasi peminjaman berhasil dibuat
            if (! $peminjaman) {
                throw new \Exception('Gagal membuat data peminjaman.');
            }

            // Create detail peminjaman
            $detailCount = 0;
            foreach ($detailBarangs as $detail) {
                $detailPeminjaman = DetailPeminjamanBarang::create([
                    'peminjaman_barang_id' => $peminjaman->id,
                    'barang_ruangan_id'    => $detail['barang_ruangan_id'],
                    'jumlah'               => $detail['jumlah'],
                ]);

                if ($detailPeminjaman) {
                    $detailCount++;
                }
            }

            // Validasi detail berhasil dibuat
            if ($detailCount !== count($detailBarangs)) {
                throw new \Exception('Gagal menyimpan beberapa detail barang.');
            }

            // Commit transaction jika semua berhasil
            DB::commit();

            // Log success
            Log::info('Peminjaman baru berhasil dibuat', [
                'id'          => $peminjaman->id,
                'kode'        => $peminjaman->kode ?? null,
                'user_id'     => $peminjaman->user_id,
                'jumlah_item' => $detailCount,
                'created_at'  => now(),
            ]);

            // Toast success
            toast(
                'Peminjaman berhasil dibuat dengan kode: ' . ($peminjaman->kode ?? $peminjaman->id) . '. Total ' . $detailCount . ' item barang.',
                'success'
            );

            return redirect()->route('backend.peminjaman.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error - tidak perlu rollback karena belum ada transaction
            Log::warning('Validasi gagal saat membuat peminjaman', [
                'errors' => $e->errors(),
                'input'  => $request->except(['_token']),
            ]);

            // Laravel otomatis handle validation exception
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // Database error - rollback transaction
            DB::rollBack();

            Log::error('Database error saat membuat peminjaman', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'sql'     => $e->getSql() ?? null,
            ]);

            toast('Terjadi kesalahan database: ' . $e->getMessage(), 'error');
            return back()->withInput();

        } catch (\Exception $e) {
            // General error - rollback transaction
            DB::rollBack();

            Log::error('Error saat membuat peminjaman', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'input'   => $request->except(['_token']),
            ]);

            // Tampilkan pesan error yang user-friendly
            toast(
                'Gagal membuat peminjaman: ' . $e->getMessage(),
                'error'
            );

            return back()->withInput();

        } catch (\Throwable $e) {
            // Catch semua error termasuk PHP errors
            DB::rollBack();

            Log::critical('Critical error saat membuat peminjaman', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            toast('Terjadi kesalahan sistem yang tidak terduga. Silakan coba lagi.', 'error');
            return back()->withInput();
        }
    }

    /**
     * Show
     */
    public function show($id)
    {
        $peminjaman = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan'])->findOrFail($id);

        // Prepare formatted fields for view
        $peminjaman->tanggal_pinjam_format  = Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y');
        $peminjaman->tanggal_kembali_format = Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y');

        return view('backend.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Edit
     */
    public function edit($id)
    {
        $peminjaman     = PeminjamanBarang::with(['detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan', 'user'])->findOrFail($id);
        $barangs        = Barang::all();
        $users          = User::all();
        $barangRuangans = BarangRuangan::with(['barang', 'ruangan'])
            ->where('status', 'tersedia')
            ->where('qty', '>', 0)
            ->get();

        return view('backend.peminjaman.edit', compact('peminjaman', 'barangs', 'users', 'barangRuangans'));
    }

/**
 * Update peminjaman with dynamic barang
 */
    public function update(Request $request, $id)
    {
        try {
            $peminjaman = PeminjamanBarang::with(['detailbarangs'])->findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'user_id'             => 'required|exists:users,id',
                'barang_ruangan_id'   => 'required|array|min:1',
                'barang_ruangan_id.*' => 'required|exists:barang_ruangans,id',
                'nama_peminjam'       => 'nullable|string|max:100',
                'instansi'            => 'nullable|string|max:100',
                'jumlah'              => 'required|array|min:1',
                'jumlah.*'            => 'required|integer|min:1',
                'tanggal_pinjam'      => 'required|date',
                'tanggal_kembali'     => 'required|date|after_or_equal:tanggal_pinjam',
                'status'              => 'required|in:menunggu,disetujui,ditolak,dikembalikan',
                'keterangan'          => 'nullable|string|max:500',
            ], [
                'required'       => ':attribute harus diisi',
                'exists'         => ':attribute tidak valid',
                'integer'        => ':attribute harus berupa angka',
                'min'            => ':attribute minimal :min',
                'array'          => ':attribute harus berupa array',
                'date'           => ':attribute harus berupa tanggal',
                'after_or_equal' => ':attribute harus setelah atau sama dengan :date',
            ]);

            $oldStatus = $peminjaman->status;
            $newStatus = $validated['status'];

            // Validasi ketersediaan untuk setiap barang ruangan baru
            $detailBarangs = [];

            foreach ($validated['barang_ruangan_id'] as $index => $barangRuanganId) {
                $jumlah = $validated['jumlah'][$index];

                $barangRuangan = BarangRuangan::with(['barang', 'ruangan'])
                    ->find($barangRuanganId);

                // Cek apakah barang ruangan ditemukan
                if (! $barangRuangan) {
                    toast('Barang ruangan tidak ditemukan.', 'error');
                    return back()->withInput();
                }

                // Cek status ketersediaan hanya jika status baru adalah 'disetujui'
                if ($newStatus === 'disetujui') {
                    if ($barangRuangan->status !== 'tersedia') {
                        toast(
                            "{$barangRuangan->barang->nama} di {$barangRuangan->ruangan->nama_ruangan}: Sedang dipinjam.",
                            'error'
                        );
                        return back()->withInput();
                    }

                    // Cek qty/stok
                    if ($jumlah > $barangRuangan->qty) {
                        toast(
                            "{$barangRuangan->barang->nama} di {$barangRuangan->ruangan->nama_ruangan}: Qty tidak mencukupi. Tersedia {$barangRuangan->qty} unit.",
                            'error'
                        );
                        return back()->withInput();
                    }
                }

                // Tambahkan ke array untuk disimpan
                $detailBarangs[] = [
                    'barang_ruangan_id' => $barangRuanganId,
                    'jumlah'            => $jumlah,
                ];
            }

            // Mulai transaction
            DB::beginTransaction();

            // Kembalikan stok barang lama jika status sebelumnya 'disetujui'
            if ($oldStatus === 'disetujui') {
                foreach ($peminjaman->detailbarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        // Tambah qty kembali
                        $barangRuangan->increment('qty', $detail->jumlah);

                        // Ubah status jadi 'tersedia' jika qty > 0
                        if ($barangRuangan->qty > 0) {
                            $barangRuangan->update(['status' => 'tersedia']);
                        }
                    }
                }
            }

            // Update peminjaman
            $peminjaman->update([
                'user_id'         => $validated['user_id'],
                'nama_peminjam'   => $validated['nama_peminjam'] ?? null,
                'instansi'        => $validated['instansi'] ?? null,
                'tanggal_pinjam'  => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status'          => $validated['status'],
                'keterangan'      => $validated['keterangan'] ?? null,
            ]);

            // Validasi peminjaman berhasil diupdate
            if (! $peminjaman) {
                throw new \Exception('Gagal memperbarui data peminjaman.');
            }

            // Hapus detail lama
            DetailPeminjamanBarang::where('peminjaman_barang_id', $peminjaman->id)->delete();

            // Create detail peminjaman baru
            $detailCount = 0;
            foreach ($detailBarangs as $detail) {
                $detailPeminjaman = DetailPeminjamanBarang::create([
                    'peminjaman_barang_id' => $peminjaman->id,
                    'barang_ruangan_id'    => $detail['barang_ruangan_id'],
                    'jumlah'               => $detail['jumlah'],
                ]);

                if ($detailPeminjaman) {
                    $detailCount++;
                }
            }

            // Validasi detail berhasil dibuat
            if ($detailCount !== count($detailBarangs)) {
                throw new \Exception('Gagal menyimpan beberapa detail barang.');
            }

            // Kurangi stok jika status baru adalah 'disetujui'
            if ($newStatus === 'disetujui') {
                foreach ($detailBarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail['barang_ruangan_id'])
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        // Kurangi qty
                        $barangRuangan->decrement('qty', $detail['jumlah']);

                        // Ubah status jadi 'sedang dipinjam' jika qty habis
                        if ($barangRuangan->qty == 0) {
                            $barangRuangan->update(['status' => 'sedang dipinjam']);
                        }
                    }
                }
            }

            // Commit transaction jika semua berhasil
            DB::commit();

            // Log success
            Log::info('Peminjaman berhasil diperbarui', [
                'id'          => $peminjaman->id,
                'kode'        => $peminjaman->kode ?? null,
                'user_id'     => $peminjaman->user_id,
                'jumlah_item' => $detailCount,
                'old_status'  => $oldStatus,
                'new_status'  => $newStatus,
                'updated_at'  => now(),
            ]);

            // Toast success
            toast(
                'Peminjaman berhasil diperbarui dengan kode: ' . ($peminjaman->kode ?? $peminjaman->id) . '. Total ' . $detailCount . ' item barang.',
                'success'
            );

            return redirect()->route('backend.peminjaman.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error - tidak perlu rollback karena belum ada transaction
            Log::warning('Validasi gagal saat update peminjaman', [
                'errors' => $e->errors(),
                'input'  => $request->except(['_token']),
            ]);

            // Laravel otomatis handle validation exception
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // Database error - rollback transaction
            DB::rollBack();

            Log::error('Database error saat update peminjaman', [
                'message' => $e->getMessage(),
                'code'    => $e->getCode(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'sql'     => $e->getSql() ?? null,
            ]);

            toast('Terjadi kesalahan database: ' . $e->getMessage(), 'error');
            return back()->withInput();

        } catch (\Exception $e) {
            // General error - rollback transaction
            DB::rollBack();

            Log::error('Error saat update peminjaman', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'input'   => $request->except(['_token']),
            ]);

            // Tampilkan pesan error yang user-friendly
            toast(
                'Gagal memperbarui peminjaman: ' . $e->getMessage(),
                'error'
            );

            return back()->withInput();

        } catch (\Throwable $e) {
            // Catch semua error termasuk PHP errors
            DB::rollBack();

            Log::critical('Critical error saat update peminjaman', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            toast('Terjadi kesalahan sistem yang tidak terduga. Silakan coba lagi.', 'error');
            return back()->withInput();
        }
    }
    /**
     * Destroy
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanBarang::with('detailbarangs')->findOrFail($id);

            // Jika status disetujui, kembalikan qty
            if ($peminjaman->status === 'disetujui') {
                foreach ($peminjaman->detailbarangs as $detail) {
                    $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                        ->lockForUpdate()
                        ->first();

                    if ($barangRuangan) {
                        // Tambah qty
                        $barangRuangan->increment('qty', $detail->jumlah);

                        // Ubah status jadi 'tersedia' jika qty > 0
                        if ($barangRuangan->qty > 0) {
                            $barangRuangan->update(['status' => 'tersedia']);
                        }
                    }
                }
            }

            // Hapus detail terlebih dahulu
            $peminjaman->detailbarangs()->delete();

            // Hapus peminjaman
            $peminjaman->delete();

            DB::commit();

            toast('Peminjaman berhasil dihapus.', 'success');
            return redirect()->route('backend.peminjaman.index');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Hapus peminjaman error: ' . $e->getMessage());
            toast('Gagal menghapus peminjaman.', 'error');
            return back();
        }
    }

    /**
     * Update status peminjaman
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak,dikembalikan',
        ]);

        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanBarang::with('detailbarangs')->findOrFail($id);
            $oldStatus  = $peminjaman->status;
            $newStatus  = $validated['status'];

            // Handle stock changes based on status
            if ($oldStatus !== $newStatus) {
                // Jika status berubah dari menunggu ke disetujui, kurangi qty & ubah status
                if ($oldStatus === 'menunggu' && $newStatus === 'disetujui') {
                    foreach ($peminjaman->detailbarangs as $detail) {
                        $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                            ->lockForUpdate()
                            ->first();

                        if ($barangRuangan) {
                            if ($barangRuangan->qty < $detail->jumlah) {
                                throw new Exception("Qty tidak mencukupi.");
                            }

                            // Kurangi qty
                            $barangRuangan->decrement('qty', $detail->jumlah);

                            // Ubah status jadi 'sedang dipinjam' jika qty habis
                            if ($barangRuangan->qty == 0) {
                                $barangRuangan->update(['status' => 'sedang dipinjam']);
                            }
                        }
                    }
                }

                // Jika status berubah ke dikembalikan atau ditolak, kembalikan qty & ubah status
                if (in_array($oldStatus, ['disetujui']) && in_array($newStatus, ['dikembalikan', 'ditolak'])) {
                    foreach ($peminjaman->detailbarangs as $detail) {
                        $barangRuangan = BarangRuangan::where('id', $detail->barang_ruangan_id)
                            ->lockForUpdate()
                            ->first();

                        if ($barangRuangan) {
                            // Tambah qty
                            $barangRuangan->increment('qty', $detail->jumlah);

                            // Ubah status jadi 'tersedia' jika qty > 0
                            if ($barangRuangan->qty > 0) {
                                $barangRuangan->update(['status' => 'tersedia']);
                            }
                        }
                    }
                }
            }

            $peminjaman->update(['status' => $newStatus]);

            DB::commit();

            toast('Status peminjaman berhasil diperbarui.', 'success');
            return redirect()->back();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Update status error: ' . $e->getMessage());
            toast($e->getMessage(), 'error');
            return back();
        }
    }
}
