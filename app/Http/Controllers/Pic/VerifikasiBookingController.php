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

   
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'kondisi_ruangan' => 'required|in:baik,kotor,rusak',
            'catatan_pic'     => 'nullable|string|max:1000',
            'foto_bukti'      => 'nullable|array|max:6',              // Array max 6
            'foto_bukti.*'    => 'image|mimes:jpeg,jpg,png|max:2048', // Each file max 2MB
        ], [
            'kondisi_ruangan.required' => 'Kondisi ruangan wajib dipilih.',
            'kondisi_ruangan.in'       => 'Nilai kondisi ruangan tidak valid.',
            'foto_bukti.array'         => 'Format foto tidak valid.',
            'foto_bukti.max'           => 'Maksimal 6 foto.',
            'foto_bukti.*.image'       => 'File harus berupa gambar.',
            'foto_bukti.*.mimes'       => 'Format gambar harus jpeg, jpg, atau png.',
            'foto_bukti.*.max'         => 'Ukuran gambar maksimal 2MB per file.',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->isVerified()) {
            return back()->with('error', 'Booking ini sudah diverifikasi.');
        }

        try {
            DB::beginTransaction();

            // Upload multiple foto bukti
            $fotoPaths = [];
            if ($request->hasFile('foto_bukti')) {
                foreach ($request->file('foto_bukti') as $index => $file) {
                    $filename    = 'verifikasi_booking_' . $id . '_' . ($index + 1) . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path        = $file->storeAs('verifikasi/booking', $filename, 'public');
                    $fotoPaths[] = $path;
                }
            }

            // Auto-set status berdasarkan kondisi
            $statusVerifikasi = match ($validated['kondisi_ruangan']) {
                'baik'  => 'diterima',
                'kotor' => 'pending',
                'rusak' => 'perlu_tindakan',
            };

            VerifikasiBooking::create([
                'booking_id'           => $id,
                'pic_id'               => Auth::id(),
                'kondisi_ruangan'      => $validated['kondisi_ruangan'],
                'catatan_pic'          => $validated['catatan_pic'],
                'foto_bukti'           => ! empty($fotoPaths) ? $fotoPaths : null, // Array or null
                'status_verifikasi'    => $statusVerifikasi,
                'tanggal_verifikasi'   => now(),
                'is_reported_to_admin' => true,
            ]);

            DB::commit();

            Log::info('Verifikasi Booking Created', [
                'booking_id'   => $id,
                'pic_id'       => Auth::id(),
                'kondisi'      => $validated['kondisi_ruangan'],
                'status'       => $statusVerifikasi,
                'total_photos' => count($fotoPaths),
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
