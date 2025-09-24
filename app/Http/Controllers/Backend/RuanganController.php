<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangan = ruangan::latest()->get();

        $title = 'Data Ruangan';
        $text  = "Apkah anda yakin ingin menghapus data ruangan ini?";
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
            'cover'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas'   => 'required|string|max:250',
            'fasilitas'   => 'required|string',
        ]);

        $ruangan = new ruangan();
        
        if ($request->hasFile('cover')) {
            $image      = $request->file('cover');
            $imageName  = time() . '_' . Str::slug($request->nama_ruangan) . '.' . $image->getClientOriginalExtension();
            $imagePath  = $image->storeAs('public/ruangan', $imageName);
            $ruangan->cover = $imageName;
        }
        $ruangan->nama_ruangan = $request->nama_ruangan;
        $ruangan->kapasitas    = $request->kapasitas;
        $ruangan->fasilitas    = $request->fasilitas;
        $ruangan->save();

        toast('Ruangan Berhasil Ditambahkan!', 'success');
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ruangan = ruangan::findOrFail($id);
        return view('backend.ruangan.show', compact('ruangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $ruangan = ruangan::findOrFail($id);
        return view('backend.ruangan.edit', compact('ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'cover'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama_ruangan' => 'required|string|max:255',
            'kapasitas'   => 'required|string|max:250',
            'fasilitas'   => 'required|string',
        ]);

        $ruangan = ruangan::findOrFail($id);

        if ($request->hasFile('cover')) {
            // Hapus gambar lama jika ada
            if ($ruangan->cover && Storage::exists('public/ruangan/' . $ruangan->cover)) {
                Storage::delete('public/ruangan/' . $ruangan->cover);
            }

            $image      = $request->file('cover');
            $imageName  = time() . '_' . Str::slug($request->nama_ruangan) . '.' . $image->getClientOriginalExtension();
            $imagePath  = $image->storeAs('public/ruangan', $imageName);
            $ruangan->cover = $imageName;
        }

        $ruangan->nama_ruangan = $request->nama_ruangan;
        $ruangan->kapasitas    = $request->kapasitas;
        $ruangan->fasilitas    = $request->fasilitas;
        $ruangan->save();

        toast('Ruangan Berhasil Diupdate!', 'success');
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ruangan = ruangan::findOrFail($id);
        // Hapus gambar lama jika ada
        if ($ruangan->cover && Storage::exists('public/ruangan/' . $ruangan->cover)) {
            Storage::delete('public/ruangan/' . $ruangan->cover);
        }

        $ruangan->delete();

        toast('Ruangan Berhasil Dihapus!', 'success');
        return redirect()->route('admin.ruangan.index')->with('success', 'Ruangan deleted successfully.');
    }
}
