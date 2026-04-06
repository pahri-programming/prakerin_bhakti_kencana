<?php
namespace App\Http\Controllers\Backend;

use App\Events\BookingExpired;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //  OTOMATIS CEK & UPDATE BOOKING EXPIRED
        $this->checkExpiredBookings();

        // Query dengan filter
        $query = Booking::with(['user', 'ruangan'])->orderBy('tanggal', 'DESC')->orderBy('created_at', 'DESC');

        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $booking = $query->paginate(15)->through(function ($b) {
            $b->tanggal_format = Carbon::parse($b->tanggal)->translatedFormat('d F Y');
            $b->hari           = Carbon::parse($b->tanggal)->translatedFormat('l');
            return $b;
        });

        $ruangan = Ruangan::orderBy('nama_ruangan')->get();

        confirmDelete('Data Booking', 'Yakin hapus booking ini?');

        return view('backend.booking.index', compact('booking', 'ruangan'));
    }

    /**
     *  CEK & UPDATE BOOKING YANG SUDAH LEWAT WAKTU
     */
    private function checkExpiredBookings()
    {
        $now = now();

        $expired = Booking::whereNotIn('status', ['Selesai', 'Ditolak'])
            ->where(function ($q) use ($now) {
                $q->where('tanggal', '<', $now->toDateString())
                    ->orWhere(function ($s) use ($now) {
                        $s->where('tanggal', $now->toDateString())
                            ->where('waktu_selesai', '<', $now->format('H:i:s'));
                    });
            })
            ->get();

        foreach ($expired as $b) {
            $b->update(['status' => 'Selesai']);
            event(new BookingExpired($b)); // Trigger event jika ada

            Log::info("Booking #{$b->kode} otomatis SELESAI (expired)");
        }
    }

    /**
     *  APPROVE BOOKING
     */
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $booking = Booking::with('ruangan')->findOrFail($id);

            // Validasi: Cek bentrok dengan booking lain yang "Diterima"
            $conflict = $this->checkBookingConflict(
                $booking->ruang_id,
                $booking->tanggal,
                $booking->waktu_mulai,
                $booking->waktu_selesai,
                $booking->id
            );

            if ($conflict) {
                toast('Tidak bisa menerima! Ada booking lain yang diterima di waktu yang sama.', 'error');
                return back();
            }

            // Update status jadi "Diterima" → Event akan auto-update status ruangan
            $booking->update(['status' => 'Diterima']);

            DB::commit();

            Log::info("Booking #{$booking->kode} DISETUJUI oleh " . auth()->user()->name);

            toast('Booking berhasil disetujui! Status ruangan otomatis diupdate.', 'success');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approve booking: ' . $e->getMessage());
            toast('Gagal menyetujui booking!', 'error');
            return back();
        }
    }

    /**
     *  REJECT BOOKING
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:250',
        ], [
            'keterangan.required' => 'Alasan penolakan harus diisi!',
        ]);

        try {
            $booking = Booking::findOrFail($id);

            $booking->update([
                'status'     => 'Ditolak',
                'keterangan' => $request->keterangan,
            ]);

            Log::info("Booking #{$booking->kode} DITOLAK oleh " . auth()->user()->name . " - Alasan: {$request->keterangan}");

            toast('Booking berhasil ditolak!', 'success');
            return back();

        } catch (\Exception $e) {
            Log::error('Error reject booking: ' . $e->getMessage());
            toast('Gagal menolak booking!', 'error');
            return back();
        }
    }

    /**
     *  SELESAIKAN BOOKING
     */
    public function complete($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            // Update status jadi "Selesai" → Event akan cek & update status ruangan
            $booking->update(['status' => 'Selesai']);

            Log::info("Booking #{$booking->kode} diselesaikan oleh " . auth()->user()->name);

            toast('Booking berhasil diselesaikan! Status ruangan otomatis diupdate.', 'success');
            return back();

        } catch (\Exception $e) {
            Log::error('Error complete booking: ' . $e->getMessage());
            toast('Gagal menyelesaikan booking!', 'error');
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ruangan = Ruangan::orderBy('nama_ruangan')->get();
        $users   = User::orderBy('name')->get();

        return view('backend.booking.create', compact('ruangan', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
        ], [
            'tanggal.after_or_equal' => 'Tanggal booking tidak boleh sebelum hari ini',
            'waktu_selesai.after'    => 'Waktu selesai harus lebih besar dari waktu mulai',
        ]);

        try {
            // Validasi waktu sudah lewat (untuk hari ini)
            if (Carbon::parse($request->tanggal)->isToday()) {
                $waktuMulai = Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);

                if ($waktuMulai->isPast()) {
                    toast('Waktu mulai sudah lewat!', 'error');
                    return back()->withInput();
                }
            }

            //  Validasi bentrok dengan booking lain
            if ($this->checkBookingConflict(
                $request->ruang_id,
                $request->tanggal,
                $request->waktu_mulai,
                $request->waktu_selesai
            )) {
                toast('Waktu booking bentrok dengan booking lain!', 'error');
                return back()->withInput();
            }

            //  Validasi bentrok dengan jadwal tetap
            if ($this->checkJadwalConflict(
                $request->ruang_id,
                $request->tanggal,
                $request->waktu_mulai,
                $request->waktu_selesai
            )) {
                toast('Waktu booking bentrok dengan jadwal tetap!', 'error');
                return back()->withInput();
            }

            //  Validasi jeda minimal 30 menit (hanya untuk user yang sama)
            if (! $this->checkMinimalGap($request->ruang_id, $request->tanggal, $request->waktu_mulai)) {
                toast('Anda harus ada jeda minimal 30 menit dari booking Anda sebelumnya!', 'error');
                return back()->withInput();
            }

            DB::beginTransaction();

            // Buat booking baru (kode auto-generated oleh Model Event)
            $booking = Booking::create([
                'user_id'       => $request->user_id,
                'ruang_id'      => $request->ruang_id,
                'tanggal'       => $request->tanggal,
                'waktu_mulai'   => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'status'        => 'Pending', // Default pending
            ]);

            DB::commit();

            Log::info("Booking baru #{$booking->kode} dibuat oleh admin untuk user #{$request->user_id}");

            toast('Data Booking berhasil disimpan dengan kode: ' . $booking->kode, 'success');
            return redirect()->route('backend.booking.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error store booking: ' . $e->getMessage());
            toast('Gagal menyimpan booking!', 'error');
            return back()->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking                 = Booking::with(['user', 'ruangan'])->findOrFail($id);
        $booking->tanggal_format = Carbon::parse($booking->tanggal)->translatedFormat('d F Y');
        $booking->hari           = Carbon::parse($booking->tanggal)->translatedFormat('l');

        return view('backend.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $booking = Booking::findOrFail($id);
        $ruangan = Ruangan::orderBy('nama_ruangan')->get();
        $users   = User::orderBy('name')->get();

        return view('backend.booking.edit', compact('booking', 'ruangan', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'status'        => 'required|in:Pending,Diterima,Ditolak,Selesai',
            'keterangan'    => 'required_if:status,Ditolak|nullable|string|max:250',
        ]);

        try {
            $booking = Booking::findOrFail($id);

            //  Validasi waktu sudah lewat (untuk hari ini)
            if (Carbon::parse($request->tanggal)->isToday()) {
                $waktuSelesai = Carbon::parse($request->tanggal . ' ' . $request->waktu_selesai);

                if ($waktuSelesai->isPast()) {
                    toast('Waktu sudah lewat!', 'error');
                    return back()->withInput();
                }
            }

            //  Validasi bentrok jika status "Diterima" dan ada perubahan waktu/ruangan
            if ($request->status === 'Diterima') {
                if ($this->checkBookingConflict(
                    $request->ruang_id,
                    $request->tanggal,
                    $request->waktu_mulai,
                    $request->waktu_selesai,
                    $id
                )) {
                    toast('Waktu booking bentrok dengan booking lain!', 'error');
                    return back()->withInput();
                }
            }

            //  Validasi jeda minimal 30 menit (exclude booking ini, hanya untuk user yang sama)
            if (! $this->checkMinimalGap($request->ruang_id, $request->tanggal, $request->waktu_mulai, $id)) {
                toast('User ini harus ada jeda minimal 30 menit dari booking sebelumnya!', 'error');
                return back()->withInput();
            }

            DB::beginTransaction();

            $oldStatus = $booking->status;

            // Update booking → Event akan auto-update status ruangan
            $booking->update([
                'user_id'       => $request->user_id,
                'ruang_id'      => $request->ruang_id,
                'tanggal'       => $request->tanggal,
                'waktu_mulai'   => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'status'        => $request->status,
                'keterangan'    => $request->status === 'Ditolak' ? $request->keterangan : null,
            ]);

            DB::commit();

            if ($oldStatus !== $request->status) {
                Log::info("Booking #{$booking->kode} status diubah dari {$oldStatus} ke {$request->status}");
            }

            toast('Data Booking berhasil diupdate!', 'success');
            return redirect()->route('backend.booking.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update booking: ' . $e->getMessage());
            toast('Gagal update booking!', 'error');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $kode    = $booking->kode;

            // Delete booking → Event akan auto-update status ruangan jika perlu
            $booking->delete();

            Log::info("Booking #{$kode} dihapus oleh " . auth()->user()->name);

            toast('Data Booking berhasil dihapus!', 'success');
            return redirect()->route('backend.booking.index');

        } catch (\Exception $e) {
            Log::error('Error delete booking: ' . $e->getMessage());
            toast('Gagal menghapus booking!', 'error');
            return back();
        }
    }

    /**
     * Export to PDF
     */
    public function export()
    {
        $query = Booking::with(['user', 'ruangan']);

        if (request()->filled('ruang_id')) {
            $query->where('ruang_id', request()->ruang_id);
        }

        if (request()->filled('tanggal')) {
            $query->whereDate('tanggal', request()->tanggal);
        }

        if (request()->filled('status')) {
            $query->where('status', request()->status);
        }

        $booking = $query->orderBy('tanggal', 'DESC')->get();

        $pdf = Pdf::loadView('backend.booking.pdfbooking', ['booking' => $booking]);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-BookingRuangan-' . now()->format('Y-m-d') . '.pdf');
    }

    //  HELPER METHODS

    /**
     *  CEK BENTROK DENGAN BOOKING LAIN
     */
    private function checkBookingConflict($ruangId, $tanggal, $waktuMulai, $waktuSelesai, $excludeId = null)
    {
        $query = Booking::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where('status', 'Diterima') // Hanya cek yang sudah diterima
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
     *  CEK BENTROK DENGAN JADWAL TETAP
     */
    private function checkJadwalConflict($ruangId, $tanggal, $waktuMulai, $waktuSelesai)
    {
        return Jadwal::where('ruang_id', $ruangId)
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
     *  CEK JEDA MINIMAL 30 MENIT (Hanya untuk USER yang SAMA)
     */
    private function checkMinimalGap($ruangId, $tanggal, $waktuMulai, $excludeId = null)
    {
        // Dapatkan user_id dari request (untuk create) atau booking yang sedang diedit
        $userId = request()->user_id ?? Booking::find($excludeId)?->user_id;

        if (! $userId) {
            return true; // Tidak bisa validasi tanpa user_id
        }

        $query = Booking::where('ruang_id', $ruangId)
            ->where('tanggal', $tanggal)
            ->where('user_id', $userId) // 🔥 HANYA CEK BOOKING USER YANG SAMA
            ->where('waktu_selesai', '<=', $waktuMulai)
            ->orderBy('waktu_selesai', 'desc');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $lastBooking = $query->first();

        if ($lastBooking) {
            $waktuKosong       = Carbon::parse($tanggal . ' ' . $lastBooking->waktu_selesai);
            $waktuMulaiBooking = Carbon::parse($tanggal . ' ' . $waktuMulai);

            // Cek apakah jeda kurang dari 30 menit
            return $waktuMulaiBooking->diffInMinutes($waktuKosong) >= 30;
        }

        return true; // Tidak ada booking sebelumnya dari user yang sama
    }
}
