<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Barang::query();

        // urutkan berdasarkan waktu dibuat
        $query->orderByDesc('created_at');

        // filter nama / kode / kategori
        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('kode', 'like', "%{$keyword}%")
                    ->orWhere('kategori', 'like', "%{$keyword}%");
            });
        }

        // filter stok kritis
        if ($request->filled('stok')) {
            if ($request->stok === 'habis') {
                $query->where('stok', '=', 0);
            } elseif ($request->stok === 'rendah') {
                $query->whereBetween('stok', [1, 5]);
            }
        }

        $barangs = $query->get()->map(function ($b) {
            $b->tanggal_input = Carbon::parse($b->created_at)->translatedFormat('d F Y');
            return $b;
        });

        $kategoris = Kategori::orderBy('nama')->get();

        confirmDelete('Data Barang', 'Yakin ingin menghapus barang ini?');
        return view('backend.barang.index', compact('barangs', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('backend.barang.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'        => 'required|string|max:50|unique:barangs,kode',
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'stok'        => 'required|integer|min:0',
            'deskripsi'   => 'nullable|string',
        ]);

        try {
            $barang              = new Barang();
            $barang->kode        = strtoupper(trim($request->kode));
            $barang->nama        = ucwords(strtolower(trim($request->nama)));
            $barang->kategori_id = $request->kategori_id;
            $barang->stok        = $request->stok;
            $barang->deskripsi   = $request->deskripsi ?: '-';
            $barang->save();

            Log::info("Barang baru ditambahkan: {$barang->nama} ({$barang->kode})");

            toast('Barang baru berhasil ditambahkan ke sistem!', 'success');
            return redirect()->route('backend.barang.index');
        } catch (\Exception $e) {
            Log::error('Gagal menambah barang: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menambah barang.', 'error');
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        return view('backend.barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'kode' => "required|max:50|unique:barangs,kode,{$barang->id}",
            'nama'      => 'required|string|max:255',
            'kategori'  => 'nullable|string|max:100',
            'stok'      => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            // Simpan data lama buat log perubahan
            $stokLama = $barang->stok;

            // Update manual biar lebih fleksibel
            $barang->kode        = strtoupper($request->kode);
            $barang->nama        = ucwords(strtolower($request->nama));
            $barang->kategori_id = $request->kategori_id;
            $barang->stok        = (int) $request->stok;
            $barang->deskripsi   = $request->deskripsi ?: $barang->deskripsi;
            $barang->save();

            // Catat jika stok berubah
            if ($stokLama != $barang->stok) {
                $selisih = $barang->stok - $stokLama;
                $logMsg  = $selisih > 0
                    ? "Stok barang {$barang->nama} bertambah {$selisih} unit."
                    : "Stok barang {$barang->nama} berkurang " . abs($selisih) . " unit.";
                Log::info($logMsg);
            }

            toast('Data barang berhasil diperbarui.', 'success');
            return redirect()->route('backend.barang.index');
        } catch (\Throwable $th) {
            Log::error("Gagal memperbarui barang ID {$id}: " . $th->getMessage());
            toast('Terjadi kesalahan saat memperbarui data.', 'error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        if ($barang->peminjamanBarang()->exists()) {
            toast('Barang tidak dapat dihapus karena sedang digunakan dalam peminjaman.', 'error');
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

    public function export()
    {
        $barangs = Barang::orderBy('nama')->get();

        if ($barangs->isEmpty()) {
            toast('Tidak ada data untuk diexport.', 'warning');
            return back();
        }

        $tanggal = Carbon::now()->translatedFormat('d F Y');
        $pdf     = Pdf::loadView('backend.barang.exportpdf', compact('barangs', 'tanggal'))
            ->setPaper('A4', 'landscape');

        return $pdf->download("data-barang-{$tanggal}.pdf");
    }
}
