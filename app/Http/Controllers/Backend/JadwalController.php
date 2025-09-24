<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\jadwal;
use App\Models\ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwal = jadwal::orderBy('tanggal', 'DESC')->get()->map(function ($jadwal) {
            $jadwal->tanggal_format = Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y');
            return $jadwal;
        });

        $title = 'Data Jadwal';
        $text  = "Apkah anda yakin ingin menghapus data jadwal ini?";
        confirmDelete($title, $text);

        return view('backend.jadwal.index', compact('jadwal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ruangan = ruangan::all();
        return view('backend.jadwal.create', compact('ruangan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruang_id'      => 'required|exists:ruangan,id',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'kegiatan'      => 'required|string|max:255',
        ]);

        $jadwal = new jadwal();
        $jadwal->ruang_id      = $request->ruang_id;
        $jadwal->tanggal       = $request->tanggal;
        $jadwal->waktu_mulai   = $request->waktu_mulai;
        $jadwal->waktu_selesai = $request->waktu_selesai;
        $jadwal->kegiatan      = $request->kegiatan;
        $jadwal->save();

        toast('Jadwal Berhasil Ditambahkan!', 'success');
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jadwal = jadwal::findOrFail($id);
        $jadwal->tanggal_format = Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y');
        return view('backend.jadwal.show', compact('jadwal')); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwal  = jadwal::findOrFail($id);
        $ruangan = ruangan::all();

        return view('backend.jadwal.edit', compact('jadwal', 'ruangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'ruang_id'      => 'required|exists:ruangan,id',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'kegiatan'      => 'required|string|max:255',
        ]);

        $jadwal = jadwal::findOrFail($id);
        $jadwal->ruang_id      = $request->ruang_id;
        $jadwal->tanggal       = $request->tanggal;
        $jadwal->waktu_mulai   = $request->waktu_mulai;
        $jadwal->waktu_selesai = $request->waktu_selesai;
        $jadwal->kegiatan      = $request->kegiatan;
        $jadwal->save();

        toast('Jadwal Berhasil Diperbarui!', 'success');
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwal = jadwal::findOrFail($id);
        $jadwal->delete();

        toast('Jadwal Berhasil Dihapus!', 'success');
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal deleted successfully.');
    }
}
