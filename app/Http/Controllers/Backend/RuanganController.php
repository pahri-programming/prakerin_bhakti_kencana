<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\jadwal;
use App\Models\ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RuanganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ruangan::query();

        //  Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        //  Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_ruangan', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $ruangan = $query->withCount([
            'booking as total_booking',
            'booking as booking_aktif' => function ($q) {
                $q->where('status', 'Diterima');
            },
            'jadwal as total_jadwal',
        ])->latest()->get();

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
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan',
            'kapasitas'    => 'required|integer|min:1',
            'lokasi'       => 'nullable|string|max:255',
        ], [
            'nama_ruangan.unique' => 'Nama ruangan sudah ada!',
            'kapasitas.min'       => 'Kapasitas minimal 1 orang',
        ]);

        try {
            DB::beginTransaction();

            $ruangan = ruangan::create([
                'nama_ruangan' => $request->nama_ruangan,
                'kapasitas'    => $request->kapasitas,
                'lokasi'       => $request->lokasi,
                'status'       => 'tersedia', // Default tersedia
            ]);

            DB::commit();

            Log::info("Ruangan '{$ruangan->nama_ruangan}' berhasil ditambahkan oleh " . auth()->user()->name);

            toast('Ruangan Berhasil Ditambahkan!', 'success');
            return redirect()->route('backend.ruangan.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error create ruangan: ' . $e->getMessage());
            toast('Gagal menambahkan ruangan!', 'error');
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ruangan = ruangan::withCount([
            'booking as total_booking',
            'booking as booking_pending'  => function ($q) {
                $q->where('status', 'Pending');
            },
            'booking as booking_diterima' => function ($q) {
                $q->where('status', 'Diterima');
            },
            'booking as booking_selesai'  => function ($q) {
                $q->where('status', 'Selesai');
            },
            'jadwal as total_jadwal',
        ])->findOrFail($id);

        //  booking terbaru di ruangan ini
        $recentBookings = $ruangan->booking()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('backend.ruangan.show', compact('ruangan', 'recentBookings'));
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
            'nama_ruangan' => 'required|string|max:255|unique:ruangans,nama_ruangan,' . $id,
            'kapasitas'    => 'required|integer|min:1',
            'lokasi'       => 'nullable|string|max:255',
            'status'       => 'required|in:tersedia,dipinjam',
        ]);

        try {
            DB::beginTransaction();

            $ruangan = ruangan::findOrFail($id);

            //  Validasi: Jangan set "tersedia" jika masih ada booking aktif
            if ($request->status === 'tersedia') {
                $adaBookingAktif = $ruangan->booking()
                    ->where('status', 'Diterima')
                    ->exists();

                if ($adaBookingAktif) {
                    toast('Tidak bisa set "Tersedia"! Masih ada booking yang diterima.', 'error');
                    return back()->withInput();
                }
            }

            $ruangan->update([
                'nama_ruangan' => $request->nama_ruangan,
                'kapasitas'    => $request->kapasitas,
                'lokasi'       => $request->lokasi,
                'status'       => $request->status,
            ]);

            DB::commit();

            Log::info("Ruangan '{$ruangan->nama_ruangan}' berhasil diupdate oleh " . auth()->user()->name);

            toast('Ruangan Berhasil Diupdate!', 'success');
            return redirect()->route('backend.ruangan.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update ruangan: ' . $e->getMessage());
            toast('Gagal update ruangan!', 'error');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $ruangan = ruangan::findOrFail($id);

            //  Cek booking yang terkait
            if ($ruangan->booking()->exists()) {
                $totalBooking = $ruangan->booking()->count();
                toast("Tidak bisa menghapus ruangan! Masih ada {$totalBooking} booking terkait.", 'error');
                return back();
            }

            //  Cek jadwal yang terkait
            if ($ruangan->jadwal()->exists()) {
                $totalJadwal = $ruangan->jadwal()->count();
                toast("Tidak bisa menghapus ruangan! Masih ada {$totalJadwal} jadwal terkait.", 'error');
                return back();
            }

            //  Cek barang ruangan yang terkait
            if ($ruangan->barangRuangan()->exists()) {
                toast('Tidak bisa menghapus ruangan! Masih ada barang terkait.', 'error');
                return back();
            }

            $namaRuangan = $ruangan->nama_ruangan;
            $ruangan->delete();

            Log::warning("Ruangan '{$namaRuangan}' dihapus oleh " . auth()->user()->name);

            toast('Ruangan Berhasil Dihapus!', 'success');
            return redirect()->route('backend.ruangan.index');

        } catch (\Exception $e) {
            Log::error('Error delete ruangan: ' . $e->getMessage());
            toast('Gagal menghapus ruangan!', 'error');
            return back();
        }
    }

    /**
     *  TOGGLE STATUS RUANGAN (AJAX)
     */
    public function toggleStatus(Request $request, string $id)
    {
        try {
            $ruangan = ruangan::findOrFail($id);

            // Jangan toggle manual jika ada booking aktif
            $adaBookingAktif = $ruangan->booking()
                ->where('status', 'Diterima')
                ->exists();

            if ($adaBookingAktif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa ubah status! Masih ada booking yang diterima.',
                ], 400);
            }

            // Toggle status
            $newStatus = $ruangan->status === 'tersedia' ? 'dipinjam' : 'tersedia';
            $ruangan->update(['status' => $newStatus]);

            Log::info("Status ruangan '{$ruangan->nama_ruangan}' diubah jadi {$newStatus} oleh " . auth()->user()->name);

            return response()->json([
                'success' => true,
                'message' => 'Status ruangan berhasil diupdate!',
                'status'  => $newStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggle status ruangan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status!',
            ], 500);
        }
    }
}
