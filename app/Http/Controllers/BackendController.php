<?php
namespace App\Http\Controllers;
use App\Models\booking;
use App\Models\jadwal;
use App\Models\PeminjamanBarang;
use App\Models\ruangan;
use Illuminate\Http\Request;


class BackendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // booking data
        $booking = \App\Models\booking::with(['user', 'ruangan'])
            ->orderByDesc('tanggal')
            ->limit(6)
            ->get()
            ->map(function ($b) {
                $b->tanggal_format = \Carbon\Carbon::parse($b->tanggal)->translatedFormat('d F Y');
                return $b;
            });

        // ✅ PERBAIKI INI - Pakai relasi yang benar
        $peminjamanBarang = \App\Models\PeminjamanBarang::with([
            'user',
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function ($p) {
                $p->tanggal_pinjam_format  = \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d F Y');
                $p->tanggal_kembali_format = \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('d F Y');
                return $p;
            });

        $counts = [
            'users'             => \App\Models\User::count(),
            'barangs'           => \App\Models\Barang::count(),
            'kategoris'         => \App\Models\Kategori::count(),
            'peminjaman'        => \App\Models\PeminjamanBarang::count(),
            'bookings'          => \App\Models\booking::count(),
            'ruangans'          => \App\Models\ruangan::count(),
            'jadwals'           => \App\Models\jadwal::count(),

            'bookingHariIni'    => \App\Models\booking::whereDate('tanggal', now()->toDateString())
                ->count(),

            'bookingPending'    => \App\Models\booking::where('status', 'Pending')->count(),

            'peminjamanHariIni' => \App\Models\PeminjamanBarang::whereDate('tanggal_pinjam', now()->toDateString())->count(),
            'PeminjamanPending' => \App\Models\PeminjamanBarang::where('status', 'menunggu')->count(),
        ];

        return view('backend.index', compact('booking', 'peminjamanBarang', 'counts'));
    }

    // ... method lainnya tetap sama
}
