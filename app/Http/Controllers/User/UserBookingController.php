<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\jadwal;
use App\Models\ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserBookingController extends Controller
{
    /**
     * LIST BOOKING MILIK USER
     */
    public function index()
    {
        $bookings = booking::with(['ruangan'])
            ->where('user_id', Auth::id())
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($b) {
                $b->tanggal_format = Carbon::parse($b->tanggal)->translatedFormat('d F Y');
                $b->hari           = Carbon::parse($b->tanggal)->translatedFormat('l');
                return $b;
            });

        return view('user.booking.index', compact('bookings'));
    }

    /**
     * FORM BUAT BOOKING
     */
    public function create()
    {
        $ruangan = ruangan::where('status', 'tersedia')
            ->orderBy('nama_ruangan')
            ->get();

        return view('user.booking.create', compact('ruangan'));
    }

    /**
     * STORE BOOKING
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'keterangan'    => 'nullable|string|max:500',

        ], [
            'ruang_id.required'      => 'ruangan harus dipilih.',
            'tanggal.after_or_equal' => 'Tanggal booking tidak boleh sebelum hari ini.',
            'waktu_selesai.after'    => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        // Validasi waktu sudah lewat (untuk hari ini)
        if (Carbon::parse($request->tanggal)->isToday()) {
            $waktuMulai = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
            if ($waktuMulai->isPast()) {
                return back()->withErrors([
                    'waktu_mulai' => 'Waktu mulai sudah lewat, tidak bisa booking.',
                ])->withInput();
            }

            // Tambahan: waktu selesai juga tidak boleh sudah lewat
            $waktuSelesai = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);
            if ($waktuSelesai->isPast()) {
                return back()->withErrors([
                    'waktu_selesai' => 'Waktu selesai sudah lewat, tidak bisa booking.',
                ])->withInput();
            }
        }

        // Validasi bentrok dengan booking lain (status Diterima)
        if ($this->checkBookingConflict(
            $request->ruang_id,
            $request->tanggal,
            $request->waktu_mulai,
            $request->waktu_selesai
        )) {
            return back()->withErrors([
                'waktu_mulai' => 'Waktu booking bentrok dengan booking lain yang sudah diterima.',
            ])->withInput();
        }

        // Validasi bentrok dengan jadwal tetap
        if ($this->checkJadwalConflict(
            $request->ruang_id,
            $request->tanggal,
            $request->waktu_mulai,
            $request->waktu_selesai
        )) {
            return back()->withErrors([
                'waktu_mulai' => 'Waktu booking bentrok dengan jadwal tetap ruangan.',
            ])->withInput();
        }

        // Validasi jeda minimal 30 menit dari booking user yang sama
        if (! $this->checkMinimalGap(
            $request->ruang_id,
            $request->tanggal,
            $request->waktu_mulai
        )) {
            return back()->withErrors([
                'waktu_mulai' => 'Harus ada jeda minimal 30 menit dari booking Anda sebelumnya.',
            ])->withInput();
        }

        DB::beginTransaction();

        try {
            $booking = booking::create([
                'user_id'       => Auth::id(),
                'ruang_id'      => $request->ruang_id,
                'tanggal'       => $request->tanggal,
                'waktu_mulai'   => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'keterangan'    => $request->keterangan ?? null,
                'status'        => 'Pending',
            ]);

            DB::commit();

            Log::info("booking baru #{$booking->kode} dibuat oleh user #" . Auth::id());

            return redirect()->route('user.booking.index')
                ->with('success', "booking berhasil diajukan! Kode booking: {$booking->kode}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store booking user: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * DETAIL BOOKING
     */
    public function show($id)
    {
        $booking = booking::with(['ruangan'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $booking->tanggal_format = Carbon::parse($booking->tanggal)->translatedFormat('d F Y');
        $booking->hari           = Carbon::parse($booking->tanggal)->translatedFormat('l');

        return view('user.booking.show', compact('booking'));
    }

    /**
     * BATALKAN BOOKING (hanya status Pending)
     */
    public function destroy($id)
    {
        $booking = booking::where('user_id', Auth::id())
            ->findOrFail($id);

        if ($booking->status !== 'Pending') {
            return back()->withErrors([
                'error' => 'booking tidak bisa dibatalkan karena sudah diproses.',
            ]);
        }

        $booking->delete();

        return back()->with('success', 'booking berhasil dibatalkan.');
    }

    // ─── HELPER METHODS ──────────────────────────────────────────────────────

    /**
     * Cek bentrok dengan booking lain yang sudah Diterima
     */
    private function checkBookingConflict($ruangId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
    {
        $query = booking::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where('status', 'Diterima')
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                    ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                    ->orWhere(function ($q2) use ($waktuMulai, $waktuSelesai) {
                        $q2->where('waktu_mulai', '<=', $waktuMulai)
                            ->where('waktu_selesai', '>=', $waktuSelesai);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Cek bentrok dengan jadwal tetap
     */
    private function checkJadwalConflict($ruangId, $tanggal, $waktuMulai, $waktuSelesai)
    {
        return jadwal::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                    ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                    ->orWhere(function ($q2) use ($waktuMulai, $waktuSelesai) {
                        $q2->where('waktu_mulai', '<=', $waktuMulai)
                            ->where('waktu_selesai', '>=', $waktuSelesai);
                    });
            })
            ->exists();
    }

    /**
     * Cek jeda minimal 30 menit dari booking user yang sama di ruangan yang sama
     */
    private function checkMinimalGap($ruangId, $tanggal, $waktuMulai, $excludeId = null)
    {
        $query = booking::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where('user_id', Auth::id())
            ->where('waktu_selesai', '<=', $waktuMulai)
            ->orderBy('waktu_selesai', 'desc');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $lastBooking = $query->first();

        if ($lastBooking) {
            $waktuKosong      = Carbon::parse($tanggal . ' ' . $lastBooking->waktu_selesai);
            $waktuMulaiBarang = Carbon::parse($tanggal . ' ' . $waktuMulai);
            return $waktuMulaiBarang->diffInMinutes($waktuKosong) >= 30;
        }

        return true;
    }
}
