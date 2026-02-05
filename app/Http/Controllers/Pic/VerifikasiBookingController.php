<?php
namespace App\Http\Controllers\Pic;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\VerifikasiBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'pic:pic']);
    }

    // ---------------------------------------------------------------
    // INDEX — list semua booking status Selesai
    // ---------------------------------------------------------------
    public function index(Request $request)
    {
        $query = Booking::with(['ruangan', 'user', 'verifikasi.pic'])
            ->where('status', 'Selesai');

        // Filter: belum_verifikasi | pending | diterima | perlu_tindakan
        if ($request->filled('status_verifikasi')) {
            if ($request->status_verifikasi === 'belum_verifikasi') {
                $query->doesntHave('verifikasi');
            } else {
                $query->whereHas('verifikasi', fn($q) =>
                    $q->where('status_verifikasi', $request->status_verifikasi)
                );
            }
        }

        // Search nama ruangan atau nama user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('ruangan', fn($r) =>
                    $r->where('nama_ruangan', 'like', "%{$search}%")
                )
                    ->orWhereHas('user', fn($u) =>
                        $u->where('name', 'like', "%{$search}%")
                    );
            });
        }

        $bookings = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('pic.verifikasi-booking.index', compact('bookings'));
    }

    // ---------------------------------------------------------------
    // CREATE — tampilkan form verifikasi
    // ---------------------------------------------------------------
    public function create($id)
    {
        $booking = Booking::with(['ruangan', 'user', 'verifikasi'])->findOrFail($id);

        if ($booking->status !== 'Selesai') {
            return redirect()->route('pic.verifikasi-booking.index')
                ->with('error', 'Hanya booking dengan status "Selesai" yang bisa diverifikasi.');
        }

        if ($booking->isVerified()) {
            return redirect()->route('pic.verifikasi-booking.show', $id)
                ->with('info', 'Booking ini sudah diverifikasi.');
        }

        return view('pic.verifikasi-booking.create', compact('booking'));
    }

    // ---------------------------------------------------------------
    // STORE — simpan hasil verifikasi
    // ---------------------------------------------------------------
    public function store(Request $request, $id)
    {
        $request->validate([
            'kondisi_ruangan' => 'required|in:baik,kotor,rusak',
            'catatan_pic'     => 'nullable|string|max:1000',
            'foto_bukti'      => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ], [
            'kondisi_ruangan.required' => 'Kondisi ruangan wajib dipilih.',
            'kondisi_ruangan.in'       => 'Nilai kondisi ruangan tidak valid.',
            'foto_bukti.image'         => 'Foto bukti harus berupa gambar.',
            'foto_bukti.mimes'         => 'Format foto harus JPG, JPEG, atau PNG.',
            'foto_bukti.max'           => 'Ukuran foto maksimal 2 MB.',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->isVerified()) {
            return back()->with('error', 'Booking ini sudah diverifikasi.');
        }

        try {
            DB::beginTransaction();

            // Upload foto jika ada
            $fotoPath = null;
            if ($request->hasFile('foto_bukti')) {
                $fotoPath = $request->file('foto_bukti')->storeAs(
                    'verifikasi/booking',
                    'vb_' . $id . '_' . time() . '.' . $request->file('foto_bukti')->extension(),
                    'public'
                );
            }

            // Auto-set status berdasarkan kondisi
            $statusVerifikasi = match ($request->kondisi_ruangan) {
                'baik'  => 'diterima',
                'kotor' => 'pending',
                'rusak' => 'perlu_tindakan',
            };

            VerifikasiBooking::create([
                'booking_id'           => $id,
                'pic_id'               => Auth::id(),
                'kondisi_ruangan'      => $request->kondisi_ruangan,
                'catatan_pic'          => $request->catatan_pic,
                'foto_bukti'           => $fotoPath,
                'status_verifikasi'    => $statusVerifikasi,
                'tanggal_verifikasi'   => now(),
                'is_reported_to_admin' => true,
            ]);

            DB::commit();

            Log::info('Verifikasi Booking Created', [
                'booking_id' => $id,
                'pic_id'     => Auth::id(),
                'kondisi'    => $request->kondisi_ruangan,
                'status'     => $statusVerifikasi,
            ]);

            $msg = $statusVerifikasi === 'diterima'
                ? 'Verifikasi berhasil. Ruangan dalam kondisi baik.'
                : 'Verifikasi berhasil. Laporan telah dikirim ke admin.';

            return redirect()->route('pic.verifikasi-booking.show', $id)
                ->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal menyimpan verifikasi booking', [
                'booking_id' => $id,
                'error'      => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Gagal menyimpan verifikasi. Silakan coba lagi.');
        }
    }

    // ---------------------------------------------------------------
    // SHOW — tampilkan detail verifikasi yang sudah selesai
    // ---------------------------------------------------------------
    public function show($id)
    {
        $booking = Booking::with(['ruangan', 'user', 'verifikasi.pic'])->findOrFail($id);

        if (! $booking->isVerified()) {
            return redirect()->route('pic.verifikasi-booking.create', $id)
                ->with('info', 'Booking ini belum diverifikasi.');
        }

        return view('pic.verifikasi-booking.show', compact('booking'));
    }
}
