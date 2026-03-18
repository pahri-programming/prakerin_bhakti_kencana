<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// 🔥 TAMBAHKAN INI

class RuanganController extends Controller
{
    /**
     * GET ALL RUANGAN - Ambil semua data ruangan
     * GET /api/ruangan
     */
    public function index(Request $request)
    {
        // Ambil semua ruangan
        $ruangan = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        // Response sukses
        return response()->json([
            'success' => true,
            'message' => 'Ruangan retrieved successfully',
            'data'    => $ruangan,
        ], 200);
    }

    /**
     * CREATE RUANGAN - Buat ruangan baru
     * POST /api/ruangan
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_ruangan' => 'required|string|max:255',
            'lokasi'       => 'nullable|string|max:255',
            'kapasitas'     => 'nullable|integer|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Buat ruangan baru
            $ruangan = Ruangan::create([
                'nama_ruangan' => $request->nama_ruangan,
                'lokasi'       => $request->lokasi,
                'kapasitas'    => $request->kapasitas,
            ]);

            // Response sukses
            return response()->json([
                'success' => true,
                'message' => 'Ruangan created successfully',
                'data'    => $ruangan,
            ], 201);
        } catch (\Exception $e) {
            // Response error
            return response()->json([
                'success' => false,
                'message' => 'Ruangan creation failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET SINGLE RUANGAN - Ambil 1 data ruangan berdasarkan ID
     * GET /api/ruangan/{id}
     */
    public function show($id)
    {
        // Cari ruangan berdasarkan ID
        $ruangan = Ruangan::find($id);

        // Kalau tidak ketemu
        if (! $ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan not found',
            ], 404);
        }

        // Response sukses
        return response()->json([
            'success' => true,
            'message' => 'Ruangan retrieved successfully',
            'data'    => $ruangan,
        ], 200);
    }

    /**
     * UPDATE RUANGAN - Update data ruangan
     * PUT/PATCH /api/ruangan/{id}
     */
    public function update(Request $request, $id)
    {
        // Cari ruangan
        $ruangan = Ruangan::find($id);

        if (! $ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan not found',
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_ruangan' => 'required|string|max:255',
            'lokasi'       => 'nullable|string|max:255',
            'kapasitas'     => 'nullable|integer|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Update ruangan
            $ruangan->update([
                'nama_ruangan' => $request->nama_ruangan,
                'lokasi'       => $request->lokasi,
                'kapasitas'    => $request->kapasitas,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ruangan updated successfully',
                'data'    => $ruangan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan update failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE RUANGAN - Hapus ruangan
     * DELETE /api/ruangan/{id}
     */
    public function destroy($id)
    {
        // Cari ruangan
        $ruangan = Ruangan::find($id);

        if (! $ruangan) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan not found',
            ], 404);
        }

        try {
            // Hapus ruangan
            $ruangan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ruangan deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan deletion failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
