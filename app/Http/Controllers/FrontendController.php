<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\barangruangan;
use App\Models\booking;
use App\Models\DendaPengembalian;
use App\Models\jadwal;
use App\Models\PeminjamanBarang;
use App\Models\ruangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    public function index()
    {
        // Ambil booking yang diterima/selesai untuk kalender
        $bookings = booking::with('ruangan', 'user')
            ->whereIn('status', ['Diterima', 'Selesai'])
            ->get();

        // Ambil SEMUA jadwal yang diinput admin (tanpa filter)
        $jadwals = jadwal::with('ruangan')->get();

        $events = [];

        // DEBUG: Log raw data dari database
        \Log::info('=== RAW DATABASE DATA ===', [
            'bookings_raw' => $bookings->toArray(),
            'jadwals_raw'  => $jadwals->toArray(),
        ]);

        // Process Bookings untuk kalender (warna orange)
        foreach ($bookings as $booking) {
            // FIX: Convert Carbon date to Y-m-d string format
            $tanggalString = \Carbon\Carbon::parse($booking->tanggal)->format('Y-m-d');

            $events[] = [
                'title'       => 'Booking - ' . ($booking->ruangan->nama_ruangan ?? 'Tanpa ruangan'),
                'start'       => $tanggalString . 'T' . $booking->waktu_mulai,
                'end'         => $tanggalString . 'T' . $booking->waktu_selesai,
                'color'       => '#ff9800',
                'description' => '<strong>📅 Booking Ruangan</strong><br>' .
                'Peminjam: ' . $booking->user->name . '<br>' .
                'Ruangan: ' . ($booking->ruangan->nama_ruangan ?? 'N/A') . '<br>' .
                'Waktu: ' . substr($booking->waktu_mulai, 0, 5) . ' - ' . substr($booking->waktu_selesai, 0, 5) . '<br>' .
                'Status: ' . $booking->status,
                'borderColor' => '#f57c00',
                'textColor'   => '#ffffff',
            ];
        }

        // Process SEMUA Jadwal admin untuk kalender (warna biru)
        foreach ($jadwals as $jadwal) {
            // FIX: Convert Carbon date to Y-m-d string format
            $tanggalString = \Carbon\Carbon::parse($jadwal->tanggal)->format('Y-m-d');

            $eventData = [
                'title'       => '📌 ' . $jadwal->kegiatan,
                'start'       => $tanggalString . 'T' . $jadwal->waktu_mulai,
                'end'         => $tanggalString . 'T' . $jadwal->waktu_selesai,
                'color'       => '#3498db',
                'description' => '<strong>📌 Jadwal Admin</strong><br>' .
                'Kegiatan: ' . $jadwal->kegiatan . '<br>' .
                'Ruangan: ' . ($jadwal->ruangan->nama_ruangan ?? 'N/A') . '<br>' .
                'Tanggal: ' . \Carbon\Carbon::parse($jadwal->tanggal)->format('d/m/Y') . '<br>' .
                'Waktu: ' . substr($jadwal->waktu_mulai, 0, 5) . ' - ' . substr($jadwal->waktu_selesai, 0, 5),
                'borderColor' => '#2980b9',
                'textColor'   => '#ffffff',
            ];

            $events[] = $eventData;

            // DEBUG: Log setiap jadwal yang diproses
            \Log::info('Jadwal Event Created', [
                'original_date'  => $jadwal->tanggal,
                'formatted_date' => $tanggalString,
                'start'          => $tanggalString . 'T' . $jadwal->waktu_mulai,
                'event'          => $eventData,
            ]);
        }

        // Data untuk tampilan beranda
        $barangRuangans = barangruangan::with(['barang.kategori', 'ruangan'])
            ->orderBy('id', 'asc')
            ->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        // DEBUG: Log final events array
        \Log::info('=== FINAL CALENDAR EVENTS ===', [
            'total_events'   => count($events),
            'bookings_count' => $bookings->count(),
            'jadwals_count'  => $jadwals->count(),
            'events_array'   => $events,
        ]);

        return view('welcome', [
            'jadwals'        => $events, // Kirim SEMUA events (booking + jadwal admin)
            'ruangans'       => $ruangans,
            'barangRuangans' => $barangRuangans,
        ]);
    }

    public function riwayat(Request $request)
    {
        // ── Booking ──────────────────────────────────────────────────
        $bookingQuery = Booking::where('user_id', Auth::id())->with('ruangan');

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

        // ── Peminjaman ────────────────────────────────────────────────
        $peminjamanQuery = PeminjamanBarang::with([
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])->where('user_id', Auth::id());

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

        // ── Denda ─────────────────────────────────────────────────────
        $denda = DendaPengembalian::with([
            'pengembalianBarang.peminjamanBarang',
            'verifikasiPengembalian',
        ])
            ->whereHas('pengembalianBarang.peminjamanBarang', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->get();

        // ── Master data untuk filter ──────────────────────────────────
        $ruangan = Ruangan::orderBy('nama_ruangan')->get();
        $barang  = Barang::orderBy('nama')->get();

        return view('riwayat', compact('booking', 'peminjaman', 'denda', 'ruangan', 'barang'));
    }

    public function exportRiwayatBooking()
    {
        $query = booking::with('ruangan')
            ->where('user_id', Auth::id());

        if (request()->filled('ruang_id')) {
            $query->where('ruang_id', request('ruang_id'));
        }
        if (request()->filled('status_booking')) {
            $query->where('status', request('status_booking'));
        }
        if (request()->filled('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }

        $booking = $query->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('riwayat_booking_pdf', compact('booking'));

        return $pdf->download('riwayat-booking-' . Auth::user()->name . '.pdf');
    }

    public function exportRiwayatPeminjaman()
    {
        $query = PeminjamanBarang::with('barang')
            ->where('user_id', Auth::id());

        if (request()->filled('barang_id')) {
            $query->where('barang_id', request('barang_id'));
        }

        if (request()->filled('status_peminjaman')) {
            $query->where('status', request('status_peminjaman'));
        }

        if (request()->filled('tanggal_pinjam')) {
            $query->whereDate('tanggal_pinjam', request('tanggal_pinjam'));
        }

        if (request()->filled('tanggal_kembali')) {
            $query->whereDate('tanggal_kembali', request('tanggal_kembali'));
        }

        $peminjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        $pdf = Pdf::loadView('riwayat_peminjaman_pdf', compact('peminjaman'));

        return $pdf->download(
            'riwayat-peminjaman-' . Auth::user()->name . '-' . now()->format('d-m-Y') . '.pdf'
        );
    }
}
