<?php

namespace App\Http\Controllers;

use App\Models\booking;
use App\Models\jadwal;
use App\Models\ruangan;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
    $booking = booking::with('ruangan')
        ->whereIn('status', ['Diterima', 'Selesai'])
        ->get();

    $jadwal = jadwal::with('ruangan')->get();

    $events = [];

    foreach ($booking as $bookings) {
    $events[] = [
        'title' => 'Booking - ' . ($bookings->ruangan->nama ?? 'Tanpa Ruangan'),
        'start' => $bookings->tanggal . 'T' . $bookings->jam_mulai,
        'end'   => $bookings->tanggal . 'T' . $bookings->jam_selesai,
        'color' => '#f39c12',
        'description' => 'Nama: ' . $bookings->user->name . '<br> Status: ' . $bookings->status,

    ];


    }

    foreach ($jadwal as $jadwals) {
        $events[] = [
            'title' => 'Jadwal - ' . ($jadwals->ruangan->nama_ruangan ?? 'Tanpa Ruangan') . ' | Deskripsi : ' . $jadwals->kegiatan,
            'start' => $jadwals->tanggal . 'T' . $jadwals->waktu_mulai,
            'end'   => $jadwals->tanggal . 'T' . $jadwals->waktu_selesai,
            'color' => '#3498db',

        ];
    }

    return view('welcome', ['jadwals' => $events]);
    }

    public function booking()
    {
        return view('booking_create');
    }


    public function riwayat(Request $request)
    {
        $query = booking::where('user_id', Auth::id())->with('ruangan');

        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $booking = $query->orderBy('tanggal', 'desc')->get();
        $ruangan = ruangan::orderBy('nama_ruangan', 'asc')->get();

        return view('bookings_riwayat', compact('booking', 'ruangan'));
    }




    public function ruanganIndex()
    {
      $ruangans = ruangan::orderBy('id', 'asc')->get();


        $title = 'Hapus Data!';
        $text  = "Apakah anda yakin ingin menghapus ruangan ini?";
        confirmDelete($title, $text);

        return view('ruangan', compact('ruangans'));
        
    }

    public function ruanganShow(string $id)
    {
        $ruangan = ruangan::findOrFail($id);
        return view('ruangan_detail', compact('ruangan'));
    }

    public function export()
    {
    $bookings = booking::where('user_id', Auth::id())->with('ruangan')->get();

    $pdf = Pdf::loadView('riwayat_pdf', ['bookings' => $bookings]);
    return $pdf->download('riwayat-booking-' . Auth::user()->name . '.pdf');
    }

}