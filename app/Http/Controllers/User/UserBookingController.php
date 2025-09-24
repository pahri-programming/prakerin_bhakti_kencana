<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\jadwal;
use App\Models\ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        $ruangan = ruangan::all();
        return view('booking_create', compact('ruangan'));
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id'    => 'required',
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'kegiatan'      => 'required|string|max:255',
        ]);

        $tanggalInput = Carbon::parse($request->tanggal)->format('Y-m-d');
        $hariIni      = Carbon::now()->format('Y-m-d');

        if ($tanggalInput === $harini) {
            $waktuSelesai = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);
            if ($waktuSelesai->lt(Carbon::now())) {
                return back()->withInput()->with('error', 'Waktu booking sudah lewat. Silakan pilih waktu yang valid.');
            }
        }

        //booking => boking bentrok
        $cekBentrok = booking::where('ruang_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                    });
            })
            ->exists();

        if ($cekBentrok) {
            toast('Jadwal Booking Bentrok !!', 'error');
            return back()->withInput()->with('error', 'Jadwal booking bentrok dengan jadwal yang sudah ada. Silakan pilih waktu lain.');
        }

        //bentrok dengan jadwal tetap
        $bentrokJadwal = jadwal::where('ruang_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                    });
            })
            ->exists();

        if ($bentrokJadwal) {
            toast('Jadwal Booking Bentrok dengan Jadwal Tetap !!', 'error');
            return back()->withInput()->with('error', 'Jadwal booking bentrok dengan jadwal tetap. Silakan pilih waktu lain.');
        }

        //waktu kosong harus ada jeda 30 menit
        $lastBooking = booking::where('ruang_id', $request->ruangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_selesai', '<=', $request->waktu_mulai)
            ->orderBy('waktu_selesai', 'desc')
            ->first();

        if ($lastBooking) {
            $waktuSelesaiTerakhir = Carbon::parse($request->tanggal . ' ' . $lastBooking->waktu_selesai);
            $waktuMulaiBaru       = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);

            if ($waktuSelesaiTerakhir->gt($waktuMulaiBaru->subMinutes(30))) {
                toast('Harus Ada Jeda 30 Menit dari Booking Sebelumnya !!', 'error');
                return back()->withInput()->with('error', 'Harus ada jeda 30 menit dari booking sebelumnya. Silakan pilih waktu lain.');
            }
        }

        $booking = new booking();
        $booking->user_id       = auth::id();
        $booking->ruang_id      = $request->ruangan_id;
        $booking->tanggal       = $request->tanggal;
        $booking->waktu_mulai   = $request->waktu_mulai;
        $booking->waktu_selesai = $request->waktu_selesai;
        $booking->status        = 'Pending';
        $booking->save();

        toast('Booking Berhasil Ditambahkan!', 'success');
        return redirect()->route('.bookings.create')->with('success', 'Booking created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function riwayat()
    {
        $booking = booking::where('user_id', Auth::id())
        ->orderBy('tanggal', 'desc')
        ->get()
        ->map(function ($item) {
            $item->tanggal_format = Carbon::parse($item->tanggal)->translatedFormat('d F Y');
            return $item;
        });

        return view('booking_riwayat', compact('booking'));
    }


    public function show(string $id)
    {
        $ruangan = ruangan::all();
        return view('ruangan', compact('ruangan'));
    }

    public function tampil(string $id)
    {
        $ruangan = ruangan::findOrFail($id);
        return view('detail_ruangan', compact('ruangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
   
}
