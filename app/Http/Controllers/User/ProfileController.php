<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PeminjamanBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $today = Carbon::today();

        // 1. Total peminjaman barang (semua status)
        $totalPinjamBarang = PeminjamanBarang::where('user_id', $user->id)->count();

        // 2. Total peminjaman ruangan (semua status)
        $totalPinjamRuangan = Booking::where('user_id', $user->id)->count();

        // 3. Total semua peminjaman
        $totalPeminjaman = $totalPinjamBarang + $totalPinjamRuangan;

        // 4. Barang yang SUDAH DIKEMBALIKAN (status: dikembalikan / selesai)
        $totalKembali = PeminjamanBarang::where('user_id', $user->id)
            ->whereIn('status', ['dikembalikan', 'selesai'])
            ->count();

        // 5. Barang yang SEDANG DIPINJAM (belum kembali)
        $belumKembali = PeminjamanBarang::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->count();

        // 6. RUANGAN YANG SEDANG DIGUNAKAN HARI INI
        $ruanganSedangDigunakan = Booking::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereIn('status', ['Diterima'])
            ->count();

        // 7. Format role untuk ditampilkan (bisa pakai isAdmin atau role)
        $roleDisplay = $user->isAdmin == 1 ? 'Admin' : 'User';
        // atau bisa juga: $roleDisplay = $user->role === 'admin' ? 'Admin' : 'User';

        return view('frontend.profile.index', compact(
            'user',
            'totalPinjamBarang',
            'totalPinjamRuangan',
            'totalPeminjaman',
            'totalKembali',
            'belumKembali',
            'ruanganSedangDigunakan',
            'roleDisplay'
        ));
    }
}
