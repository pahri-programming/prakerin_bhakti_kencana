<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DendaBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserDendaBookingController extends Controller
{
    public function index()
    {
        $dendas = DendaBooking::with([
            'booking.ruangan',
            'verifikasiBooking',
        ])
            ->whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();

        $stats = [
            'total_tagihan' => $dendas->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi'])->sum('jumlah_denda'),
            'total_lunas'   => $dendas->where('status_pembayaran', 'sudah_bayar')->sum('jumlah_denda'),
            'jumlah_aktif'  => $dendas->whereIn('status_pembayaran', ['belum_bayar', 'menunggu_verifikasi'])->count(),
        ];

        return view('user.denda-booking.index', compact('dendas', 'stats'));
    }

    public function show($id)
    {
        $denda = DendaBooking::with([
            'booking.ruangan',
            'booking.user',
            'verifikasiBooking.pic',
            'admin',
        ])
            ->whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        return view('user.denda-booking.show', compact('denda'));
    }

    public function uploadBukti(Request $request, $id)
    {
        $denda = DendaBooking::whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        if ($denda->isBayar()) {
            return back()->withErrors(['error' => 'Denda ini sudah lunas.']);
        }
        if ($denda->isDibebaskan()) {
            return back()->withErrors(['error' => 'Denda ini sudah dibebaskan.']);
        }
        if ($denda->isMenungguVerifikasi()) {
            return back()->withErrors(['error' => 'Bukti sudah dikirim, menunggu verifikasi admin.']);
        }

        $request->validate([
            'bukti_pembayaran'      => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'keterangan_pembayaran' => 'nullable|string|max:500',
            'tanggal_bayar'         => 'required|date|before_or_equal:today',
        ]);

        DB::beginTransaction();

        try {
        if ($denda->bukti_pembayaran) {
                Storage::disk('public')->delete($denda->bukti_pembayaran);
            }

            $path = $request->file('bukti_pembayaran')
                ->store('bukti-pembayaran/booking', 'public');

            $denda->update([
                'bukti_pembayaran'      => $path,
                'tanggal_bayar'         => $request->tanggal_bayar,
                'keterangan_pembayaran' => $request->keterangan_pembayaran,
                'status_pembayaran'     => 'menunggu_verifikasi',
            ]);

            DB::commit();

            return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error upload bukti denda booking: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
