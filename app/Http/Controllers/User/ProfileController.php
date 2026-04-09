<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\PeminjamanBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        $totalPinjamBarang      = PeminjamanBarang::where('user_id', $user->id)->count();
        $totalPinjamRuangan     = booking::where('user_id', $user->id)->count();
        $totalPeminjaman        = $totalPinjamBarang + $totalPinjamRuangan;
        $totalKembali           = PeminjamanBarang::where('user_id', $user->id)->whereIn('status', ['dikembalikan', 'selesai'])->count();
        $belumKembali           = PeminjamanBarang::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $ruanganSedangDigunakan = booking::where('user_id', $user->id)->whereDate('tanggal', $today)->whereIn('status', ['Diterima'])->count();
        $roleDisplay            = $user->isAdmin == 1 ? 'Admin' : 'User';

        return view('frontend.profile.index', compact(
            'user', 'totalPinjamBarang', 'totalPinjamRuangan',
            'totalPeminjaman', 'totalKembali', 'belumKembali',
            'ruanganSedangDigunakan', 'roleDisplay'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        $user->update([
            'name'     => $request->name,
            'instansi' => $request->instansi,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
