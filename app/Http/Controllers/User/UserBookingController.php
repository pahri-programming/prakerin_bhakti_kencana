<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\jadwal;
use App\Models\ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{

    public function export()
    {
        $query = booking::where('user_id', Auth::id())->with('ruangan');

        if (request()->filled('ruang_id')) {
            $query->where('ruang_id', request('ruang_id'));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('tanggal')) {
            $query->whereDate('tanggal', request('tanggal'));
        }

        $bookings = $query->orderBy('tanggal', 'desc')->get();

        $pdf = Pdf::loadView('riwayat_pdf', ['bookings' => $bookings]);
        return $pdf->download('riwayat-booking-' . Auth::user()->name . '.pdf');
    }
    /**
     * Display a listing of the resource.
     */
    public function create()
    {
        $ruangans = ruangan::orderBy('nama_ruangan', 'asc')->get();
        return view('booking_create', compact('ruangans'));
    }

    /**
     * Store booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ]);

        $tanggal    = Carbon::parse($request->tanggal);
        $jamMulai   = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
        $jamSelesai = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);

        // Cek jam mulai < waktu sekarang (kalau tanggal hari ini)
        if ($tanggal->isToday() && $jamMulai->lt(now())) {
            return back()->withInput()->with('error', 'Jam mulai harus setelah waktu sekarang.');
        }

        // Cek bentrok dengan jadwal tetap
        $jadwalTetaps = Jadwal::where('ruang_id', $request->ruang_id)->get();
        foreach ($jadwalTetaps as $jadwal) {
            if (
                ($request->waktu_mulai >= $jadwal->waktu_mulai && $request->waktu_mulai < $jadwal->waktu_selesai) ||
                ($request->waktu_selesai > $jadwal->waktu_mulai && $request->waktu_selesai <= $jadwal->waktu_selesai) ||
                ($request->waktu_mulai <= $jadwal->waktu_mulai && $request->waktu_selesai >= $jadwal->waktu_selesai)
            ) {
                return back()->withInput()->with('error', 'Jadwal bentrok dengan jadwal tetap ruangan.');
            }
        }

        // Cek bentrok booking lain
        $cekBentrok = booking::where('ruang_id', $request->ruang_id)
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
            return back()->withInput()->with('error', 'Jadwal bentrok dengan booking lain.');
        }

        // Jeda 30 menit
        $lastBooking = booking::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_selesai', '<=', $request->waktu_mulai)
            ->orderBy('waktu_selesai', 'desc')
            ->first();

        if ($lastBooking) {
            $lastEnd = Carbon::parse($request->tanggal . ' ' . $lastBooking->waktu_selesai);
            if ($lastEnd->gt($jamMulai->subMinutes(30))) {
                return back()->withInput()->with('error', 'Harus ada jeda 30 menit setelah pemakaian sebelumnya.');
            }
        }

        // Simpan booking
        booking::create([
            'user_id'       => Auth::id(),
            'ruang_id'      => $request->ruang_id,
            'tanggal'       => $request->tanggal,
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'status'        => 'Pending',
        ]);


        return redirect()->route('bookings.create')->with('success', 'booking berhasil diajukan.');
    }
}
