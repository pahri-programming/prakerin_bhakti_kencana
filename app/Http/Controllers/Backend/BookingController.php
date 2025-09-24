<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\ruangan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

// Import PDF facade

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function export()
    {
        $filter = booking::with(['user', 'ruangan']);

        if (request()->filled('ruang_id')) {
            $filter->where('ruang_id', request()->ruang_id);
        }

        if (request()->filled('tanggal')) {
            $filter->where('tanggal', request()->tanggal);
        }

        if (request()->filled('status')) {
            $filter->where('status', request()->status);
        }

        $booking = $filter->orderBy('status')->get();

        $pdf = PDF::loadView('backend.booking.pdfbooking', ['booking' => $booking]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('laporan-data-booking.pdf');
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        booking::where(function ($query) {
            $query->where('tanggal', '<', now()->toDateString())->orWhere(function ($q) {
                $q->where('tanggal', now()->toDateString())
                    ->where('waktu_selesai', '<', now()->format('H:i:s'));
            });
        })
            ->where('status', '!=', 'Selesai')
            ->update(['status' => 'Selesai']);

        //mengabil filter
        $query = booking::with(['user', 'ruangan'])->ordeyBy('tanggal', 'DESC');

        if (request()->filled('ruang_id')) {
            $query->where('ruang_id', request()->ruang_id);
        }
        if (request()->filled('tanggal')) {
            $query->where('tanggal', request()->tanggal);
        }
        if (request()->filled('status')) {
            $query->where('status', request()->status);
        }

        // format tanggal
        $booking = $query->get()->map(function ($booking) {
            $booking->tanggal_format = Carbon::parse($booking->tanggal)->translatedFormat('d F Y');
            return $booking;
        });

        $ruangan = ruangan::all();

        confirmDelete('Data Booking', 'Apakah anda yakin ingin menghapus data booking ini?');
        return view('backend.booking.index', compact('booking', 'ruangan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ruangan = ruangan::all();
        $users   = User::all();
        return view('backend.booking.create', compact('ruangan', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //jam terlewat
        $tanggalInput = Carbon::parse($request->tanggal)->format('Y-m-d');
        $hariIni      = Carbon::now()->format('Y-m-d');

        if ($tanggalInput === $hariIni) {
            $jamselesai = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);
            if ($jamselesai->lt(Carbon::now())) {
                toast('jam Sudah Lewat !!', 'error');
                return back()->withInput()->with('error', 'Waktu booking sudah lewat. Silakan pilih waktu yang valid.');
            }
        }

        //booking bentrok
        $betrok = booking::where('ruang_id', $request->ruang_id)
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

        if ($betrok) {
            toast('Waktu booking bentrok dengan booking lain !!', 'error');
            return back()->withInput()->with('error', 'Waktu booking bentrok dengan booking lain. Silakan pilih waktu yang berbeda.');
        }

        //betrok dengan jadwal tetap
        $betrokkjadwal = jadwal::where('ruang_id', $request->ruang_id)
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

        if ($betrokkjadwal) {
            toast('Waktu booking bentrok dengan jadwal tetap !!', 'error');
            return back()->withInput()->with('error', 'Waktu booking bentrok dengan jadwal tetap. Silakan pilih waktu yang berbeda.');
        }

        //waktu kosong harus 30 menit
        $lastBooking = booking::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('waktu_selesai', '<=', $request->waktu_mulai)
            ->orderBy('waktu_selesai', 'desc')
            ->first();

        if ($lastBooking) {
            $waktuKosong = Carbon::parse($request->tanggal . ' ' . $lastBooking->waktu_selesai);
            $waktuMulai  = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);

            if ($waktuKosong->gt($waktuMulai->subMinutes(30))) {
                toast('Harus ada jeda booking minimal 30 menit setelah pemakaian sebelumnya !!', 'error');
                return back()->withInput()->with('error', 'Waktu kosong antara booking minimal 30 menit. Silakan pilih waktu yang berbeda.');
            }
        }

        $request->validate([
            'user_id'       => 'required',
            'ruang_id'      => 'required',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'status'        => 'required|in:Pending,Disetujui,Ditolak,Selesai',
        ]);

        $booking                = new booking();
        $booking->user_id       = $request->user_id;
        $booking->ruang_id      = $request->ruang_id;
        $booking->tanggal       = $request->tanggal;
        $booking->waktu_mulai   = $request->waktu_mulai;
        $booking->waktu_selesai = $request->waktu_selesai;
        $booking->status        = 'Pending';
        $booking->save();

        toast('Data Booking berhasil disimpan', 'success');
        return redirect()->route('backend.booking.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking                 = booking::with(['user', 'ruangan'])->findOrFail($id);
        $booking->tanggal_format = Carbon::parse($booking->tanggal)->translatedFormat('d F Y');
        return view('backend.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = booking::findOrFail($id);
        $ruangan = ruangan::all();
        $users   = User::all();
        return view('backend.booking.edit', compact('booking', 'ruangan', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        //jam terlewat
        $tanggalInput = Carbon::parse($request->tanggal)->format('Y-m-d');
        $hariIni      = Carbon::now()->format('Y-m-d');

        if ($tanggalInput === $hariIni) {
            $jamselesai = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);
            if ($jamselesai->it(Carbon::now())) {
                toast('jam Sudah Lewat !!', 'error');
                return back()->withInput()->with('error', 'Waktu booking sudah lewat. Silakan pilih waktu yang valid.');
            }
        }

        //booking bentrok
        $betrok = booking::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $id) // Exclude the current booking
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                    });
            })
            ->exists();

        if ($betrok) {
            toast('Waktu booking bentrok dengan booking lain !!', 'error');
            return back()->withInput()->with('error', 'Waktu booking bentrok dengan booking lain. Silakan pilih waktu yang berbeda.');
        }

        //waktu kosong harus 30 menit
        $lastBooking = booking::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $id) // Exclude the current booking
            ->where('waktu_selesai', '<=', $request->waktu_mulai)
            ->orderBy('waktu_selesai', 'desc')
            ->first();

        if ($lastBooking) {
            $waktuKosong = Carbon::parse($request->tanggal . ' ' . $lastBooking->waktu_selesai);
            $waktuMulai  = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);

            if ($waktuKosong->gt($waktuMulai->subMinutes(30))) {
                toast('Harus ada jeda booking minimal 30 menit setelah pemakaian sebelumnya !!', 'error');
                return back()->withInput()->with('error', 'Waktu kosong antara booking minimal 30 menit. Silakan pilih waktu yang berbeda.');
            }
        }

        $request->validate([
            'user_id'       => 'required',
            'ruang_id'      => 'required',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'status'        => 'required|in:Pending,Disetujui,Ditolak,Selesai',
        ]);

        $booking                = booking::findOrFail($id);
        $booking->user_id       = $request->user_id;
        $booking->ruang_id      = $request->ruang_id;
        $booking->tanggal       = $request->tanggal;
        $booking->waktu_mulai   = $request->waktu_mulai;
        $booking->waktu_selesai = $request->waktu_selesai;
        $booking->status        = $request->status;
        $booking->save();

        toast('Data Booking berhasil diupdate', 'success');
        return redirect()->route('backend.booking.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = booking::findOrFail($id);
        $booking->delete();

        toast('Data Booking berhasil dihapus', 'success');
        return redirect()->route('backend.booking.index');
    }
}
