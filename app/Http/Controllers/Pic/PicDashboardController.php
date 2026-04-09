<?php
namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\PengembalianBarang;
use App\Models\VerifikasiBooking;
use App\Models\verifikasipengembalian;
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
            // booking Stats
            'booking_perlu_verifikasi'      => booking::where('status', 'selesai')
                ->doesntHave('verifikasi')
                ->count(),

            'booking_sudah_verifikasi'      => VerifikasiBooking::where('pic_id', Auth::id())->count(),

            'booking_bermasalah'            => VerifikasiBooking::where('pic_id', Auth::id())
                ->whereIn('kondisi_ruangan', ['rusak_ringan', 'rusak_berat'])
                ->count(),

            // Pengembalian Stats
            'pengembalian_perlu_verifikasi' => PengembalianBarang::where('status', 'menunggu_pic')
                ->doesntHave('verifikasi')
                ->whereHas('detailpengembalians', function ($q) {
                    $q->where('status_awal', 'bermasalah');
                })
                ->count(),

            'pengembalian_sudah_verifikasi' => verifikasipengembalian::where('pic_id', Auth::id())->count(),

            'pengembalian_bermasalah'       => verifikasipengembalian::where('pic_id', Auth::id())
                ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang'])
                ->count(),
        ];

        // Pending Items - booking
        $pendingBooking = booking::with(['user', 'ruangan', 'verifikasi'])
            ->where('status', 'selesai')
            ->doesntHave('verifikasi')
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // Pending Items - Pengembalian
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

        // Recent Verifikasi - booking
        $recentVerifikasiBooking = VerifikasiBooking::with(['booking.user', 'booking.ruangan'])
            ->where('pic_id', Auth::id())
            ->orderBy('tanggal_verifikasi', 'desc')
            ->take(5)
            ->get();

        // Recent Verifikasi - Pengembalian
        $recentVerifikasiPengembalian = verifikasipengembalian::with([
            'pengembalianBarang.peminjamanBarang.user',
            'pengembalianBarang.detailpengembalians.barang',
        ])
            ->where('pic_id', Auth::id())
            ->orderBy('tanggal_verifikasi', 'desc')
            ->take(5)
            ->get();

        return view('pic.dashboard', compact(
            'stats',
            'pendingBooking',
            'pendingPengembalian',
            'recentVerifikasiBooking',
            'recentVerifikasiPengembalian'
        ));
    }
}
