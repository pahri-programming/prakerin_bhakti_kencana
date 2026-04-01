<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display listing with filters
     */
    public function index(Request $request)
    {
        $query = Barang::with('kategori')->orderByDesc('created_at');

        // Filter pencarian nama
        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhereHas('kategori', function ($q2) use ($keyword) {
                        $q2->where('nama', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter stok (via barang_ruangans)
        if ($request->filled('stok')) {
            if ($request->stok === 'habis') {
                $query->whereDoesntHave('barangruangan', function ($q) {
                    $q->where('qty', '>', 0);
                });
            } elseif ($request->stok === 'rendah') {
                $query->whereHas('barangruangan', function ($q) {
                    $q->whereBetween('qty', [1, 5]);
                });
            }
        }

        $barangs = $query->get()->map(function ($b) {
            $b->created_at_format = Carbon::parse($b->created_at)->translatedFormat('d F Y');
            return $b;
        });

        $kategoris = Kategori::orderBy('nama')->get();

        confirmDelete('Data Barang', 'Yakin ingin menghapus barang ini?');

        return view('backend.barang.index', compact('barangs', 'kategoris'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('backend.barang.create', compact('kategoris'));
    }

    /**
     * Store new barang
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255|unique:barangs,nama',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'harga'       => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
        ], [
            'nama.required' => 'Nama barang harus diisi',
            'nama.unique'   => 'Barang dengan nama ini sudah ada',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min'     => 'Harga tidak boleh negatif',
        ]);

        try {
            $barang = Barang::create([
                'nama'        => ucwords(strtolower(trim($validated['nama']))),
                'kategori_id' => $validated['kategori_id'] ?? null,
                'harga'       => $validated['harga'] ?? 0,
                'keterangan'  => $validated['keterangan'] ?? '-',
            ]);

            Log::info("Barang baru ditambahkan: {$barang->nama} - Harga: {$barang->harga_format}");

            toast('Barang baru berhasil ditambahkan ke sistem!', 'success');
            return redirect()->route('backend.barang.index');

        } catch (\Exception $e) {
            Log::error('Gagal menambah barang: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menambah barang.', 'error');
            return back()->withInput();
        }
    }

    /**
     * Show detail
     */
    public function show($id)
    {
        $barang                    = Barang::with('kategori')->findOrFail($id);
        $barang->created_at_format = Carbon::parse($barang->created_at)->translatedFormat('d F Y');

        return view('backend.barang.show', compact('barang'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $barang    = Barang::findOrFail($id);
        $kategoris = Kategori::orderBy('nama')->get();

        return view('backend.barang.edit', compact('barang', 'kategoris'));
    }

    /**
     * Update barang
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'nama'        => 'required|string|max:255|unique:barangs,nama,' . $id,
            'kategori_id' => 'nullable|exists:kategoris,id',
            'harga'       => 'nullable|numeric|min:0',
            'keterangan'  => 'nullable|string',
        ], [
            'nama.required' => 'Nama barang harus diisi',
            'nama.unique'   => 'Barang dengan nama ini sudah ada',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min'     => 'Harga tidak boleh negatif',
        ]);

        try {
            $barang->update([
                'nama'        => ucwords(strtolower(trim($validated['nama']))),
                'kategori_id' => $validated['kategori_id'] ?? null,
                'harga'       => $validated['harga'] ?? 0,
                'keterangan'  => $validated['keterangan'] ?? $barang->keterangan,
            ]);

            Log::info("Barang diupdate: {$barang->nama} - Harga baru: {$barang->harga_format}");

            toast('Data barang berhasil diperbarui.', 'success');
            return redirect()->route('backend.barang.index');

        } catch (\Exception $e) {
            Log::error("Gagal memperbarui barang ID {$id}: " . $e->getMessage());
            toast('Terjadi kesalahan saat memperbarui data.', 'error');
            return back()->withInput();
        }
    }

    /**
     * Delete barang
     */
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        // Check relasi
        if ($barang->peminjaman()->exists()) {
            toast('Barang tidak dapat dihapus karena sedang digunakan dalam peminjaman.', 'error');
            return back();
        }

        if ($barang->barangruangan()->exists()) {
            toast('Barang tidak dapat dihapus karena masih terdaftar di ruangan.', 'error');
            return back();
        }

        try {
            $nama = $barang->nama;
            $barang->delete();

            Log::warning("Barang dihapus: {$nama}");
            toast('Barang berhasil dihapus dari sistem.', 'success');

        } catch (\Exception $e) {
            Log::error('Gagal menghapus barang: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menghapus barang.', 'error');
        }

        return back();
    }

    /**
     * Export PDF
     */
    public function export()
    {
        $barangs = Barang::with('kategori')->orderByDesc('created_at')->get()->map(function ($b) {
            $b->created_at_format = Carbon::parse($b->created_at)->translatedFormat('d F Y');

            // Prepare base64 image for PDF
            if ($b->foto && Storage::disk('public')->exists($b->foto)) {
                $fullPath = Storage::disk('public')->path($b->foto);
                try {
                    $contents       = file_get_contents($fullPath);
                    $mime           = mime_content_type($fullPath) ?: 'image/jpeg';
                    $b->foto_base64 = 'data:' . $mime . ';base64,' . base64_encode($contents);
                } catch (\Throwable $e) {
                    Log::warning("Gagal baca file foto untuk barang {$b->id}: {$e->getMessage()}");
                    $b->foto_base64 = null;
                }
            } else {
                $b->foto_base64 = null;
            }

            return $b;
        });

        $tanggal = Carbon::now()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('backend.barang.exportpdf', compact('barangs', 'tanggal'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-data-barang-' . Carbon::now()->format('Ymd_His') . '.pdf');
    }
}
