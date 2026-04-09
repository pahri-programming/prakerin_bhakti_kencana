<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\barangruangan;
use App\Models\ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangRuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = barangruangan::with(['barang', 'ruangan']);

        // Filter by search (barang atau ruangan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%");
                })
                    ->orWhereHas('ruangan', function ($q) use ($search) {
                        $q->where('nama_ruangan', 'like', "%{$search}%")
                            ->orWhere('lokasi', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by ruangan
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        $barangRuangans = $query->latest()->paginate(10);
        $ruangans       = ruangan::orderBy('nama_ruangan')->get();

        return view('backend.barangruangan.index', compact('barangRuangans', 'ruangans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs  = Barang::all();
        $ruangans = ruangan::all();

        return view('backend.barangruangan.create', compact('barangs', 'ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_id'  => 'required|exists:barangs,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'qty'        => 'required|integer|min:1',
            'status'     => 'required|in:tersedia,sedang dipinjam',
        ], [
            'barang_id.required'  => 'Barang harus dipilih',
            'barang_id.exists'    => 'Barang tidak ditemukan',
            'ruangan_id.required' => 'Ruangan harus dipilih',
            'ruangan_id.exists'   => 'Ruangan tidak ditemukan',
            'qty.required'        => 'Jumlah harus diisi',
            'qty.integer'         => 'Jumlah harus berupa angka',
            'qty.min'             => 'Jumlah minimal 1',
            'status.required'     => 'Status harus dipilih',
            'status.in'           => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        barangruangan::create($request->all());

        return redirect()->route('backend.barangruangan.index')
            ->with('success', 'Data barang ruangan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $barangRuangan = barangruangan::with(['barang', 'ruangan'])->findOrFail($id);

        return view('backend.barangruangan.show', compact('barangRuangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $barangRuangan = barangruangan::findOrFail($id);
        $barangs       = Barang::all();
        $ruangans      = ruangan::all();

        return view('backend.barangruangan.edit', compact('barangRuangan', 'barangs', 'ruangans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'barang_id'  => 'required|exists:barangs,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'qty'        => 'required|integer|min:1',
            'status'     => 'required|in:tersedia,sedang dipinjam',
        ], [
            'barang_id.required'  => 'Barang harus dipilih',
            'barang_id.exists'    => 'Barang tidak ditemukan',
            'ruangan_id.required' => 'Ruangan harus dipilih',
            'ruangan_id.exists'   => 'Ruangan tidak ditemukan',
            'qty.required'        => 'Jumlah harus diisi',
            'qty.integer'         => 'Jumlah harus berupa angka',
            'qty.min'             => 'Jumlah minimal 1',
            'status.required'     => 'Status harus dipilih',
            'status.in'           => 'Status tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $barangRuangan = barangruangan::findOrFail($id);
        $barangRuangan->update($request->all());

        return redirect()->route('backend.barangruangan.index')
            ->with('success', 'Data barang ruangan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $barangRuangan = barangruangan::findOrFail($id);
        $barangRuangan->delete();

        return redirect()->route('backend.barangruangan.index')
            ->with('success', 'Data barang ruangan berhasil dihapus');
    }

    /**
     * Update status barang ruangan
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:tersedia,sedang dipinjam',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $barangRuangan = barangruangan::findOrFail($id);
        $barangRuangan->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', 'Status berhasil diupdate');
    }

    /**
     * Get barang by ruangan (untuk AJAX)
     */
    public function getBarangByRuangan($ruangan_id)
    {
        $barangs = barangruangan::with('barang')
            ->where('ruangan_id', $ruangan_id)
            ->get();

        return response()->json($barangs);
    }
}
