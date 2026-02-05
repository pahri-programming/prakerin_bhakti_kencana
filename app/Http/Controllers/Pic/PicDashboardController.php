<?php
namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PeminjamanBarang;
use App\Models\VerifikasiBooking;
use App\Models\VerifikasiPeminjaman;
use Illuminate\Support\Facades\Auth;

class PicDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'pic:pic']);
    }

    public function index()
    {
        $picId = Auth::id();

        // Statistics
        $stats = [
            // Peminjaman
            'peminjaman_perlu_verifikasi' => PeminjamanBarang::where('status', 'dikembalikan')
                ->doesntHave('verifikasi')
                ->count(),
            'peminjaman_sudah_verifikasi' => VerifikasiPeminjaman::where('pic_id', $picId)->count(),
            'peminjaman_bermasalah'       => VerifikasiPeminjaman::where('pic_id', $picId)
                ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang'])
                ->count(),

            // Booking
            'booking_perlu_verifikasi'    => Booking::where('status', 'Selesai')
                ->doesntHave('verifikasi')
                ->count(),
            'booking_sudah_verifikasi'    => VerifikasiBooking::where('pic_id', $picId)->count(),
            'booking_bermasalah'          => VerifikasiBooking::where('pic_id', $picId)
                ->whereIn('kondisi_ruangan', ['kotor', 'rusak'])
                ->count(),
        ];

        // Recent Verifikasi Peminjaman
        // Ganti peminjaman.barang jadi peminjaman.detailbarangs.barangRuangan.barang
        $recentVerifikasiPeminjaman = VerifikasiPeminjaman::with([
                'peminjaman.user',
                'peminjaman.detailbarangs.barangRuangan.barang', // 👈 Relasi yang benar
                'peminjaman.detailbarangs.barangRuangan.ruangan'
            ])
            ->where('pic_id', $picId)
            ->latest('tanggal_verifikasi')
            ->take(5)
            ->get();

        // Recent Verifikasi Booking
        $recentVerifikasiBooking = VerifikasiBooking::with(['booking.ruangan', 'booking.user'])
            ->where('pic_id', $picId)
            ->latest('tanggal_verifikasi')
            ->take(5)
            ->get();

        // Pending Verifikasi Peminjaman
        // Ganti 'barang' jadi 'detailbarangs.barangRuangan.barang'
        $pendingPeminjaman = PeminjamanBarang::with([
                'user',
                'detailbarangs.barangRuangan.barang', // 👈 Relasi yang benar
                'detailbarangs.barangRuangan.ruangan'
            ])
            ->where('status', 'dikembalikan')
            ->doesntHave('verifikasi')
            ->latest('tanggal_kembali')
            ->take(10)
            ->get();

        // Pending Verifikasi Booking
        $pendingBooking = Booking::with(['ruangan', 'user'])
            ->where('status', 'Selesai')
            ->doesntHave('verifikasi')
            ->latest('tanggal')
            ->take(10)
            ->get();

        return view('pic.dashboard', compact(
            'stats',
            'recentVerifikasiPeminjaman',
            'recentVerifikasiBooking',
            'pendingPeminjaman',
            'pendingBooking'
        ));
    }
}