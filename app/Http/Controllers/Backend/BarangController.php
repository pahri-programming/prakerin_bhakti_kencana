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
    // export pdf
    public function export()
    {
        $barangs = Barang::with('kategori')->orderByDesc('created_at')->get()->map(function ($b) {
            $b->created_at_format = Carbon::parse($b->created_at)->translatedFormat('d F Y');

            // prepare base64 image (safe for DomPDF)
            if ($b->foto && \Storage::disk('public')->exists($b->foto)) {
                $fullPath = \Storage::disk('public')->path($b->foto); // absolute path
                try {
                    $contents       = file_get_contents($fullPath);
                    $mime           = mime_content_type($fullPath) ?: 'image/jpeg';
                    $b->foto_base64 = 'data:' . $mime . ';base64,' . base64_encode($contents);
                } catch (\Throwable $e) {
                    \Log::warning("Gagal baca file foto untuk barang {$b->id}: {$e->getMessage()}");
                    $b->foto_base64 = null;
                }
            } else {
                $b->foto_base64 = null;
            }

            return $b;
        });

        $tanggal = Carbon::now()->translatedFormat('d F Y');

        // dd($barangs->first()->foto_base64);
        $pdf = Pdf::loadView('backend.barang.exportpdf', compact('barangs', 'tanggal'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-data-barang-' . Carbon::now()->format('Ymd_His') . '.pdf');
    }

    // public function export()
    // {
    //     $barangs = Barang::with('kategori')->orderByDesc('created_at')->get()->map(function ($b) {
    //         $b->created_at_format = Carbon::parse($b->created_at)->translatedFormat('d F Y');
    //         return $b;
    //     });

    //     foreach ($barangs as $b) {
    //         $b->foto_url = $b->foto ? Storage::url($b->foto) : null;
    //     }

    //     $tanggal = Carbon::now()->translatedFormat('d F Y'); // <— tambahkan ini

    //     $pdf = Pdf::loadView('backend.barang.exportpdf', compact('barangs', 'tanggal'));
    //     $pdf->setPaper('A4', 'landscape');
    //     return $pdf->download('laporan-data-barang-' . Carbon::now()->format('Ymd_His') . '.pdf');
    // }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Barang::with('kategori')->orderByDesc('created_at');

        // Filter pencarian nama / kode
        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                    ->orWhere('kode', 'like', "%{$keyword}%");
            })
            // juga cari di nama kategori jika user mengetik nama kategori
                ->orWhereHas('kategori', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%{$keyword}%");
                });
        }

        // Filter berdasarkan kategori id (select dropdown mengirim param name="kategori")
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // filter stok (opsional)
        if ($request->filled('stok')) {
            if ($request->stok === 'habis') {
                $query->where('stok', 0);
            } elseif ($request->stok === 'rendah') {
                $query->whereBetween('stok', [1, 5]);
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

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('backend.barang.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'keterangan'  => 'nullable|string',
        ]);

        try {
            $barang = new Barang();
            $barang->nama        = ucwords(strtolower(trim($request->nama)));
            $barang->kategori_id = $request->kategori_id;
            $barang->keterangan  = $request->keterangan ?: '-';
            $barang->save();

            Log::info("Barang baru ditambahkan: {$barang->nama}");

            toast('Barang baru berhasil ditambahkan ke sistem!', 'success');
            return redirect()->route('backend.barang.index');
        } catch (\Exception $e) {
            Log::error('Gagal menambah barang: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menambah barang.', 'error');
            return back()->withInput();
        }
    }

    // show
    public function show($id)
    {
        $barang                    = Barang::with('kategori')->findOrFail($id);
        $barang->created_at_format = Carbon::parse($barang->created_at)->translatedFormat('d F Y');
        return view('backend.barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang    = Barang::findOrFail($id);
        $kategoris = Kategori::orderBy('nama')->get();
        return view('backend.barang.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kode' => "required|max:50|unique:barangs,kode,{$barang->id}",
            'nama'        => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'stok'        => 'required|integer|min:0',
            'keterangan'  => 'nullable|string',
        ]);

        try {
            // handle foto upload
            if ($request->hasFile('foto')) {
                // hapus foto lama jika ada
                if ($barang->foto && \Storage::disk('public')->exists($barang->foto)) {
                    \Storage::disk('public')->delete($barang->foto);
                }
                $file     = $request->file('foto');
                $ext      = $file->getClientOriginalExtension();
                $filename = 'barang_' . time() . '_' . uniqid() . '.' . $ext;
                // menyimpan di storage/app/public/barangs -> dapat diakses via asset('storage/'.$barang->foto)
                $path         = $file->storeAs('barangs', $filename, 'public');
                $barang->foto = $path;
            }
            // Simpan data lama buat log perubahan
            $stokLama = $barang->stok;

            // Update manual biar lebih fleksibel
            $barang->kode        = strtoupper($request->kode);
            $barang->nama        = ucwords(strtolower($request->nama));
            $barang->kategori_id = $request->kategori_id;
            $barang->stok        = (int) $request->stok;
            $barang->keterangan  = $request->keterangan ?: $barang->keterangan;
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

        // hapus file foto lama jika ada
        if ($barang->foto && \Storage::disk('public')->exists($barang->foto)) {
            try {
                \Storage::disk('public')->delete($barang->foto);
            } catch (\Exception $e) {
                Log::error("Gagal menghapus file foto barang ({$barang->nama}): " . $e->getMessage());
            }
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

}
