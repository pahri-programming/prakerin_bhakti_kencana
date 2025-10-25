<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Kategori::query()->orderByDesc('created_at');

        if ($request->filled('search')) {
            $keyword = trim($request->search);
            $query->where('nama', 'like', "%{$keyword}%");
        }

        $kategoris = $query->get();

        confirmDelete('Data Kategori', 'Yakin ingin menghapus kategori ini?');

        return view('backend.kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('backend.kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategoris,nama',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            $kategori = new Kategori();
            $kategori->nama = ucwords(strtolower($request->nama));
            $kategori->deskripsi = $request->deskripsi ?: '-';
            $kategori->save();

            Log::info("Kategori baru dibuat: {$kategori->nama}");

            toast('Kategori baru berhasil ditambahkan.', 'success');
            return redirect()->route('backend.kategori.index');
        } catch (\Exception $e) {
            Log::error('Gagal menambah kategori: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menambah kategori.', 'error');
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return view('backend.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama' => "required|string|max:100|unique:kategoris,nama,{$kategori->id}",
            'deskripsi' => 'nullable|string',
        ]);

        try {
            $kategori->nama = ucwords(strtolower($request->nama));
            $kategori->deskripsi = $request->deskripsi ?: '-';
            $kategori->save();

            Log::info("Kategori diperbarui: {$kategori->nama}");
            toast('Kategori berhasil diperbarui.', 'success');
            return redirect()->route('backend.kategori.index');
        } catch (\Throwable $th) {
            Log::error("Gagal update kategori ID {$id}: " . $th->getMessage());
            toast('Terjadi kesalahan saat memperbarui kategori.', 'error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Cegah hapus kalau masih dipakai barang
        if ($kategori->barangs()->exists()) {
            toast('Kategori tidak dapat dihapus karena masih digunakan oleh barang.', 'error');
            return back();
        }

        try {
            $nama = $kategori->nama;
            $kategori->delete();
            Log::warning("Kategori dihapus: {$nama}");
            toast('Kategori berhasil dihapus.', 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus kategori: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menghapus kategori.', 'error');
        }

       toast('Kategori Berhasil Dihapus!', 'success');
        return redirect()->route('backend.kategori.index')->with('success', 'Kategori deleted successfully.');
    }
}
       