<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PeminjamanBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileApiController extends Controller
{
    /**
     * GET /api/profile
     * Data profil + statistik user
     */
    public function index(Request $request)
    {
        $user  = $request->user();
        $today = Carbon::today();

        $totalPeminjamanBarang = PeminjamanBarang::where('user_id', $user->id)->count();
        $totalBookingRuangan   = Booking::where('user_id', $user->id)->count();
        $totalKembali          = PeminjamanBarang::where('user_id', $user->id)
            ->whereIn('status', ['dikembalikan', 'selesai'])->count();
        $belumKembali = PeminjamanBarang::where('user_id', $user->id)
            ->where('status', 'disetujui')->count();
        $bookingAktif = Booking::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->whereIn('status', ['Diterima'])->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'instansi' => $user->instansi,
                'role'     => $user->role,
                'stats'    => [
                    'total_peminjaman_barang' => $totalPeminjamanBarang,
                    'total_booking_ruangan'   => $totalBookingRuangan,
                    'total_kembali'           => $totalKembali,
                    'belum_kembali'           => $belumKembali,
                    'booking_aktif_hari_ini'  => $bookingAktif,
                ],
            ],
        ], 200);
    }

    /**
     * PUT /api/profile
     * Update nama dan instansi
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'instansi' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $user->update([
            'name'     => $request->name,
            'instansi' => $request->instansi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'instansi' => $user->instansi,
                'role'     => $user->role,
            ],
        ], 200);
    }

    /**
     * PUT /api/profile/password
     * Ganti password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8',
            'konfirmasi'    => 'required|same:password_baru',
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min'      => 'Password baru minimal 8 karakter.',
            'konfirmasi.same'        => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui',
        ], 200);
    }
}
