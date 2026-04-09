<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\booking;
use App\Models\DendaPengembalian;
use App\Models\DendaBooking;
use App\Models\PeminjamanBarang;
use App\Models\ruangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Halaman riwayat utama
     */
    public function index(Request $request)
    {
        // ── booking ───────────────────────────────────────────────
        $bookingQuery = booking::with('ruangan')
            ->where('user_id', Auth::id());

        if ($request->filled('ruang_id')) {
            $bookingQuery->where('ruang_id', $request->ruang_id);
        }
        if ($request->filled('status_booking')) {
            $bookingQuery->where('status', $request->status_booking);
        }
        if ($request->filled('tanggal')) {
            $bookingQuery->whereDate('tanggal', $request->tanggal);
        }

        $booking = $bookingQuery->orderBy('tanggal', 'desc')->get();

        // ── Peminjaman ────────────────────────────────────────────
        $peminjamanQuery = PeminjamanBarang::with([
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])->where('user_id', Auth::id());

        // Filter by ruangan
        if ($request->filled('ruangan_pinjam_id')) {
            $peminjamanQuery->whereHas('detailbarangs.barangRuangan', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_pinjam_id);
            });
        }

        // Filter by barang (via barang_ruangan → barang_id)
        if ($request->filled('barang_id')) {
            $peminjamanQuery->whereHas('detailbarangs.barangRuangan', function ($q) use ($request) {
                $q->where('barang_id', $request->barang_id);
            });
        }

        if ($request->filled('status_peminjaman')) {
            $peminjamanQuery->where('status', $request->status_peminjaman);
        }
        if ($request->filled('tanggal_pinjam')) {
            $peminjamanQuery->whereDate('tanggal_pinjam', $request->tanggal_pinjam);
        }
        if ($request->filled('tanggal_kembali')) {
            $peminjamanQuery->whereDate('tanggal_kembali', $request->tanggal_kembali);
        }

        $peminjaman = $peminjamanQuery->orderBy('tanggal_pinjam', 'desc')->get();

        // ── Denda Peminjaman ──────────────────────────────────────
        $denda = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang',
            'verifikasiPengembalian',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->get();
        // ── Denda booking ─────────────────────────────────────────
        $dendaBooking = DendaBooking::with([
            'booking.ruangan',
            'verifikasiBooking',
        ])
            ->whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();

        // ── Master data untuk filter ──────────────────────────────
        $ruangan = ruangan::orderBy('nama_ruangan')->get();
        $barang  = Barang::orderBy('nama')->get();

        return view('user.riwayat.index', compact(
            'booking', 'peminjaman', 'denda', 'dendaBooking', 'ruangan', 'barang'
        ));

    }

    /**
     * Export riwayat booking ke PDF
     */
    public function exportBooking(Request $request)
    {
        $query = booking::with('ruangan')
            ->where('user_id', Auth::id());

        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }
        if ($request->filled('status_booking')) {
            $query->where('status', $request->status_booking);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $booking = $query->orderBy('tanggal', 'desc')->get();
        $user    = Auth::user();

        $pdf = Pdf::loadView('user.riwayat.pdf-booking', compact('booking', 'user'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('riwayat-booking-' . $user->name . '-' . now()->format('d-m-Y') . '.pdf');
    }

    /**
     * Export riwayat peminjaman ke PDF
     */
    public function exportPeminjaman(Request $request)
    {
        $query = PeminjamanBarang::with([
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])->where('user_id', Auth::id());

        if ($request->filled('ruangan_pinjam_id')) {
            $query->whereHas('detailbarangs.barangRuangan', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_pinjam_id);
            });
        }
        if ($request->filled('barang_id')) {
            $query->whereHas('detailbarangs.barangRuangan', function ($q) use ($request) {
                $q->where('barang_id', $request->barang_id);
            });
        }
        if ($request->filled('status_peminjaman')) {
            $query->where('status', $request->status_peminjaman);
        }
        if ($request->filled('tanggal_pinjam')) {
            $query->whereDate('tanggal_pinjam', $request->tanggal_pinjam);
        }
        if ($request->filled('tanggal_kembali')) {
            $query->whereDate('tanggal_kembali', $request->tanggal_kembali);
        }

        $peminjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();
        $user       = Auth::user();

        $pdf = Pdf::loadView('user.riwayat.pdf-peminjaman', compact('peminjaman', 'user'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('riwayat-peminjaman-' . $user->name . '-' . now()->format('d-m-Y') . '.pdf');
    }
}
