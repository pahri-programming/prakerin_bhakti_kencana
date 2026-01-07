<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangan = Ruangan::latest()->get();

        $title = 'Data Ruangan';
        $text  = "Apakah anda yakin ingin menghapus data ruangan ini?";
        confirmDelete($title, $text);

        return view('backend.ruangan.index', compact('ruangan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.ruangan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas'    => 'required|string|max:250',
            'lokasi'       => 'nullable|string|max:255',
        ]);

        $ruangan               = new Ruangan();
        $ruangan->nama_ruangan = $request->nama_ruangan;
        $ruangan->kapasitas    = $request->kapasitas;
        $ruangan->lokasi       = $request->lokasi;
        $ruangan->status       = $request->status ?? 'tersedia';
        $ruangan->save();

        toast('Ruangan Berhasil Ditambahkan!', 'success');
        return redirect()->route('backend.ruangan.index')->with('success', 'Ruangan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('backend.ruangan.show', compact('ruangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('backend.ruangan.edit', compact('ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas'    => 'required|string|max:250',
            'lokasi'       => 'nullable|string|max:255',
            'status'       => 'required|in:tersedia,dipinjam',
        ]);

        $ruangan               = Ruangan::findOrFail($id);
        $ruangan->nama_ruangan = $request->nama_ruangan;
        $ruangan->kapasitas    = $request->kapasitas;
        $ruangan->lokasi       = $request->lokasi;
        $ruangan->status       = $request->status;
        $ruangan->save();

        toast('Ruangan Berhasil Diupdate!', 'success');
        return redirect()->route('backend.ruangan.index')->with('success', 'Ruangan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        // Cek booking yang terkait
        if ($ruangan->booking()->exists()) {
            toast('Tidak bisa menghapus ruangan karena masih ada booking terkait!', 'error');
            return back();
        }

        // Cek barang ruangan yang terkait
        if ($ruangan->barangRuangan()->exists()) {
            toast('Tidak bisa menghapus ruangan karena masih ada barang terkait!', 'error');
            return back();
        }

        // Hapus data ruangan
        $ruangan->delete();

        toast('Ruangan Berhasil Dihapus!', 'success');
        return redirect()->route('backend.ruangan.index')->with('success', 'Ruangan deleted successfully.');
    }

    /**
     * Update status ruangan
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:tersedia,dipinjam',
        ]);

        $ruangan         = Ruangan::findOrFail($id);
        $ruangan->status = $request->status;
        $ruangan->save();

        toast('Status Ruangan Berhasil Diupdate!', 'success');
        return redirect()->back();
    }
}
