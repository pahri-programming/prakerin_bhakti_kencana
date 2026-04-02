<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\barangruangan;
use App\Models\booking;
use App\Models\DendaPengembalian;
use App\Models\DendaBooking;
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
}
