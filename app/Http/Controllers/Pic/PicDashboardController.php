<?php
namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PeminjamanBarang;
use App\Models\PengembalianBarang;
use App\Models\VerifikasiBooking;
use App\Models\VerifikasiPeminjaman;
use App\Models\VerifikasiPengembalian;
use Illuminate\Support\Facades\Auth;

class PicDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'pic:pic']);
    }

    public function index()
    {
        // Statistics
        $stats = [
            // Peminjaman Stats
            'peminjaman_perlu_verifikasi'   => PeminjamanBarang::whereHas('pengembalian', function ($q) {
                $q->where('status', 'menunggu_pic')
                    ->doesntHave('verifikasi');
            })->count(),

            'peminjaman_sudah_verifikasi'   => VerifikasiPeminjaman::where('pic_id', Auth::id())->count(),

            'peminjaman_bermasalah'         => VerifikasiPeminjaman::where('pic_id', Auth::id())
                ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang'])
                ->count(),

            // Booking Stats
            'booking_perlu_verifikasi'      => Booking::where('status', 'selesai')
                ->doesntHave('verifikasi')
                ->count(),

            'booking_sudah_verifikasi'      => VerifikasiBooking::where('pic_id', Auth::id())->count(),

            'booking_bermasalah'            => VerifikasiBooking::where('pic_id', Auth::id())
                ->whereIn('kondisi_ruangan', ['rusak_ringan', 'rusak_berat'])
                ->count(),

            // Pengembalian Stats (NEW!)
            'pengembalian_perlu_verifikasi' => PengembalianBarang::where('status', 'menunggu_pic')
                ->doesntHave('verifikasi')
                ->whereHas('detailpengembalians', function ($q) {
                    $q->where('status_awal', 'bermasalah');
                })
                ->count(),

            'pengembalian_sudah_verifikasi' => VerifikasiPengembalian::where('pic_id', Auth::id())->count(),

            'pengembalian_bermasalah'       => VerifikasiPengembalian::where('pic_id', Auth::id())
                ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang'])
                ->count(),
        ];

        // Pending Items - Peminjaman
        $pendingPeminjaman = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'pengembalian.verifikasi'])
            ->whereHas('pengembalian', function ($q) {
                $q->where('status', 'menunggu_pic')
                    ->doesntHave('verifikasi');
            })
            ->orderBy('tanggal_kembali', 'asc')
            ->take(5)
            ->get();

        // Pending Items - Booking
        $pendingBooking = Booking::with(['user', 'ruangan', 'verifikasi'])
            ->where('status', 'selesai')
            ->doesntHave('verifikasi')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // Pending Items - Pengembalian (NEW!)
        $pendingPengembalian = PengembalianBarang::with([
            'peminjamanBarang.user',
            'detailpengembalians.barang',
            'verifikasi',
        ])
            ->where('status', 'menunggu_pic')
            ->doesntHave('verifikasi')
            ->whereHas('detailpengembalians', function ($q) {
                $q->where('status_awal', 'bermasalah');
            })
            ->orderBy('tanggal_kembali', 'desc')
            ->take(5)
            ->get();

        // Recent Verifikasi - Peminjaman
        $recentVerifikasiPeminjaman = VerifikasiPeminjaman::with(['peminjaman.user', 'peminjaman.detailbarangs.barangRuangan.barang'])
            ->where('pic_id', Auth::id())
            ->orderBy('tanggal_verifikasi', 'desc')
            ->take(5)
            ->get();

        // Recent Verifikasi - Booking
        $recentVerifikasiBooking = VerifikasiBooking::with(['booking.user', 'booking.ruangan'])
            ->where('pic_id', Auth::id())
            ->orderBy('tanggal_verifikasi', 'desc')
            ->take(5)
            ->get();

        // Recent Verifikasi - Pengembalian (NEW!)
        $recentVerifikasiPengembalian = VerifikasiPengembalian::with([
            'pengembalianBarang.peminjamanBarang.user',
            'pengembalianBarang.detailpengembalians.barang',
        ])
            ->where('pic_id', Auth::id())
            ->orderBy('tanggal_verifikasi', 'desc')
            ->take(5)
            ->get();

        return view('pic.dashboard', compact(
            'stats',
            'pendingPeminjaman',
            'pendingBooking',
            'pendingPengembalian',
            'recentVerifikasiPeminjaman',
            'recentVerifikasiBooking',
            'recentVerifikasiPengembalian'
        ));
    }
}
