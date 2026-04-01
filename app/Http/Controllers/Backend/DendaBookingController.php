<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DendaBooking;
use App\Models\VerifikasiBooking;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DendaBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = VerifikasiBooking::with(['booking.ruangan', 'booking.user', 'pic', 'denda'])
            ->whereIn('kondisi_ruangan', ['kotor', 'rusak']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('booking', function ($q) use ($search) {
                $q->whereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi_ruangan', $request->kondisi);
        }

        if ($request->filled('status_denda')) {
            if ($request->status_denda === 'belum_ada') {
                $query->doesntHave('denda');
            } else {
                $query->has('denda');
            }
        }

        $verifikasi = $query->orderBy('tanggal_verifikasi', 'desc')->paginate(10);

        return view('backend.denda-booking.index', compact('verifikasi'));
    }

    /**
     * TINDAK LANJUT: Form input denda booking
     */
    public function tindakLanjut($id)
    {
        $verifikasi = VerifikasiBooking::with([
            'booking.ruangan',
            'booking.user',
            'pic',
            'denda',
        ])->findOrFail($id);

        return view('backend.denda-booking.tindak-lanjut', compact('verifikasi'));
    }

    /**
     * STORE: Simpan denda booking
     */
    public function store(Request $request, $id)
    {
        try {
            $verifikasi = VerifikasiBooking::with(['booking', 'denda'])->findOrFail($id);

            if ($verifikasi->denda) {
                toast('Denda sudah pernah dibuat untuk booking ini.', 'error');
                return back();
            }

            $validated = $request->validate([
                'jumlah_denda'      => 'required|numeric|min:0',
                'keterangan_denda'  => 'nullable|string|max:1000',
                'tindakan_admin'    => 'required|string|max:1000',
                'status_pembayaran' => 'required|in:belum_bayar,dibebaskan',
            ], [
                'jumlah_denda.required'   => 'Jumlah denda harus diisi.',
                'tindakan_admin.required' => 'Tindakan admin harus diisi.',
            ]);

            DB::beginTransaction();

            DendaBooking::create([
                'booking_id'            => $verifikasi->booking_id,
                'verifikasi_booking_id' => $verifikasi->id,
                'jumlah_denda'          => $validated['jumlah_denda'],
                'keterangan_denda'      => $validated['keterangan_denda'] ?? null,
                'tindakan_admin'        => $validated['tindakan_admin'],
                'status_pembayaran'     => $validated['status_pembayaran'],
                'tanggal_tindakan'      => now(),
                'admin_id'              => auth()->id(),
            ]);

            // Update status verifikasi
            $verifikasi->update([
                'status_verifikasi' => 'perlu_tindakan',
                'tindakan_admin'    => $validated['tindakan_admin'],
            ]);

            DB::commit();

            $msg = $validated['status_pembayaran'] === 'dibebaskan'
                ? 'Denda dibebaskan. Tidak ada kewajiban pembayaran.'
                : 'Denda berhasil ditetapkan. User harus melakukan pembayaran.';

            toast($msg, 'success');
            return redirect()->route('backend.denda-booking.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error store denda booking: ' . $e->getMessage());
            toast('Gagal menyimpan denda: ' . $e->getMessage(), 'error');
            return back()->withInput();
        }
    }

    /**
     * APPROVE BUKTI: Admin konfirmasi pembayaran user
     */
    public function approveBukti(Request $request, $id)
    {
        try {
            $denda = DendaBooking::findOrFail($id);

            if ($denda->status_pembayaran !== 'menunggu_verifikasi') {
                toast('Status denda tidak valid untuk diapprove.', 'error');
                return back();
            }

            DB::beginTransaction();

            $denda->update([
                'status_pembayaran'    => 'sudah_bayar',
                'verifikator_bayar_id' => auth()->id(),
            ]);

            DB::commit();

            Log::info('Bukti bayar denda booking di-approve', [
                'denda_id' => $denda->id,
                'admin_id' => auth()->id(),
            ]);

            toast('Pembayaran denda booking berhasil dikonfirmasi.', 'success');
            return back();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error approve bukti denda booking: ' . $e->getMessage());
            toast('Gagal konfirmasi: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * REJECT BUKTI: Admin tolak bukti bayar user
     */
    public function rejectBukti(Request $request, $id)
    {
        try {
            $denda = DendaBooking::findOrFail($id);

            if ($denda->status_pembayaran !== 'menunggu_verifikasi') {
                toast('Status denda tidak valid untuk ditolak.', 'error');
                return back();
            }

            $request->validate([
                'alasan_tolak' => 'required|string|max:500',
            ], [
                'alasan_tolak.required' => 'Alasan penolakan harus diisi.',
            ]);

            DB::beginTransaction();

            if ($denda->bukti_pembayaran) {
                Storage::disk('public')->delete($denda->bukti_pembayaran);
            }

            $denda->update([
                'status_pembayaran'     => 'belum_bayar',
                'bukti_pembayaran'      => null,
                'tanggal_bayar'         => null,
                'keterangan_pembayaran' => null,
                'tindakan_admin'        => 'Bukti ditolak: ' . $request->alasan_tolak,
            ]);

            DB::commit();

            toast('Bukti pembayaran ditolak. User perlu upload ulang.', 'warning');
            return back();

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error reject bukti denda booking: ' . $e->getMessage());
            toast('Gagal menolak bukti: ' . $e->getMessage(), 'error');
            return back();
        }
    }

    /**
     * LIST: Semua denda booking
     */
    public function listDenda(Request $request)
    {
        $query = DendaBooking::with([
            'booking.ruangan',
            'booking.user',
            'verifikasiBooking.pic',
            'admin',
            'verifikatorBayar',
        ]);

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('booking', function ($q) use ($search) {
                $q->whereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $denda = $query->latest()->paginate(10);

        $stats = [
            'total_denda'         => DendaBooking::sum('jumlah_denda'),
            'belum_bayar'         => DendaBooking::belumBayar()->sum('jumlah_denda'),
            'menunggu_verifikasi' => DendaBooking::menungguVerifikasi()->count(),
            'sudah_bayar'         => DendaBooking::sudahBayar()->sum('jumlah_denda'),
            'total_transaksi'     => DendaBooking::count(),
        ];

        return view('backend.denda-booking.list', compact('denda', 'stats'));
    }
}
