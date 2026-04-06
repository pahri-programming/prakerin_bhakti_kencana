<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingApiController extends Controller
{
    /**
     * GET /api/booking
     * Daftar semua booking milik user yang login
     */
    public function index(Request $request)
    {
        $bookings = Booking::with(['ruangan'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $data = $bookings->map(fn($b) => $this->formatBooking($b));

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * GET /api/booking/ruangan
     * Daftar ruangan yang tersedia untuk dibooking
     */
    public function ruanganTersedia()
    {
        $ruangans = Ruangan::where('status', 'tersedia')->get();

        $data = $ruangans->map(function ($r) {
            return [
                'id'           => $r->id,
                'nama_ruangan' => $r->nama_ruangan,
                'kapasitas'    => $r->kapasitas ?? null,
                'lokasi'       => $r->lokasi ?? null,
                'status'       => $r->status,
                'foto'         => $r->foto ? asset('storage/' . $r->foto) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ], 200);
    }

    /**
     * POST /api/booking
     * Ajukan booking ruangan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruang_id'      => 'required|exists:ruangans,id',
            'tanggal'       => 'required|date|after_or_equal:today',
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        // Cek waktu sudah lewat (untuk hari ini)
        if (\Carbon\Carbon::parse($request->tanggal)->isToday()) {
            $waktuMulai = \Carbon\Carbon::parse($request->tanggal . ' ' . $request->waktu_mulai);
            if ($waktuMulai->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu mulai sudah lewat, tidak bisa booking',
                ], 422);
            }
        }

        // Cek ruangan tersedia
        $ruangan = Ruangan::find($request->ruang_id);
        if ($ruangan->status !== 'tersedia') {
            return response()->json([
                'success' => false,
                'message' => "Ruangan {$ruangan->nama_ruangan} sedang tidak tersedia",
            ], 422);
        }

        // Cek konflik jadwal di tanggal & waktu yang sama
        $konflik = Booking::where('ruang_id', $request->ruang_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', 'Diterima')
            ->where(function ($q) use ($request) {
                $q->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai]);
            })
            ->exists();

        if ($konflik) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan sudah dibooking di waktu tersebut',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id'       => $request->user()->id,
                'ruang_id'      => $request->ruang_id,
                'tanggal'       => $request->tanggal,
                'waktu_mulai'   => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'keterangan'    => $request->keterangan,
                'status'        => 'Pending',
            ]);

            DB::commit();

            $booking->load('ruangan');

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil diajukan, menunggu persetujuan admin',
                'data'    => $this->formatBooking($booking),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API store booking error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/booking/{id}
     * Detail booking milik user
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::with(['ruangan'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatBooking($booking),
        ], 200);
    }

    /**
     * DELETE /api/booking/{id}
     * Batalkan booking (hanya yang masih Pending)
     */
    public function destroy(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->find($id);

        if (! $booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan',
            ], 404);
        }

        if ($booking->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak bisa dibatalkan karena sudah diproses',
            ], 422);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan',
        ], 200);
    }

    /**
     * Helper: Format data booking untuk response JSON
     */
    private function formatBooking($b)
    {
        return [
            'id'            => $b->id,
            'kode'          => $b->kode,
            'status'        => $b->status,
            'tanggal'       => $b->tanggal,
            'waktu_mulai'   => $b->waktu_mulai,
            'waktu_selesai' => $b->waktu_selesai,
            'keterangan'    => $b->keterangan,
            'created_at'    => $b->created_at,
            'ruangan'       => [
                'id'           => $b->ruangan->id ?? null,
                'nama_ruangan' => $b->ruangan->nama_ruangan ?? '-',
                'lokasi'       => $b->ruangan->lokasi ?? null,
                'foto'         => $b->ruangan->foto
                    ? asset('storage/' . $b->ruangan->foto)
                    : null,
            ],
        ];
    }
}
