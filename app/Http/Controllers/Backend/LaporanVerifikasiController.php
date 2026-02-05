<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\VerifikasiBooking;
use App\Models\VerifikasiPeminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanVerifikasiController extends Controller
{
    public function __construct()
    {
        // Hanya admin yang bisa akses
        $this->middleware(['auth', 'admin']);
    }

    /**
     * ==========================================
     * LAPORAN VERIFIKASI PEMINJAMAN BARANG
     * ==========================================
     */

    /**
     * Tampilkan semua laporan verifikasi peminjaman dari PIC
     */
    public function laporanPeminjaman(Request $request)
    {
        $query = VerifikasiPeminjaman::with([
            'peminjaman.user',
            'peminjaman.detailbarangs.barangRuangan.barang',
            'pic',
        ])->orderBy('created_at', 'DESC');

        // Filter berdasarkan kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Filter berdasarkan status verifikasi
        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('created_at', [
                $request->tanggal_dari . ' 00:00:00',
                $request->tanggal_sampai . ' 23:59:59',
            ]);
        }

        $laporan = $query->paginate(15);

        // Statistik
        $stats = [
            'total'          => VerifikasiPeminjaman::count(),
            'baik'           => VerifikasiPeminjaman::where('kondisi', 'baik')->count(),
            'rusak_ringan'   => VerifikasiPeminjaman::where('kondisi', 'rusak_ringan')->count(),
            'rusak_berat'    => VerifikasiPeminjaman::where('kondisi', 'rusak_berat')->count(),
            'hilang'         => VerifikasiPeminjaman::where('kondisi', 'hilang')->count(),
            'pending'        => VerifikasiPeminjaman::where('status_verifikasi', 'pending')->count(),
            'perlu_tindakan' => VerifikasiPeminjaman::whereIn('kondisi', ['rusak_berat', 'hilang'])
                ->where('status_verifikasi', 'pending')
                ->count(),
        ];

        return view('backend.verifikasi.laporan-peminjaman', compact('laporan', 'stats'));
    }

    /**
     * Detail laporan verifikasi peminjaman
     */
    public function detailPeminjaman($id)
    {
        $verifikasi = VerifikasiPeminjaman::with([
            'peminjaman.user',
            'peminjaman.detailbarangs.barangRuangan.barang',
            'pic',
        ])->findOrFail($id);

        return view('backend.verifikasi.detail-peminjaman', compact('verifikasi'));
    }

    /**
     * Admin input tindakan untuk laporan peminjaman
     */
    public function tindakanPeminjaman(Request $request, $id)
    {
        $request->validate([
            'tindakan_admin'    => 'required|string|max:1000',
            'status_verifikasi' => 'required|in:diterima,perlu_tindakan',
        ]);

        try {
            DB::beginTransaction();

            $verifikasi = VerifikasiPeminjaman::findOrFail($id);

            $verifikasi->update([
                'tindakan_admin'    => $request->tindakan_admin,
                'status_verifikasi' => $request->status_verifikasi,
            ]);

            DB::commit();

            Log::info('Admin Update Tindakan Verifikasi Peminjaman', [
                'verifikasi_id' => $id,
                'admin_id'      => auth()->id(),
                'tindakan'      => $request->tindakan_admin,
                'status'        => $request->status_verifikasi,
            ]);

            // Jika status jadi "perlu_tindakan" dan kondisi rusak berat/hilang
            if ($request->status_verifikasi === 'perlu_tindakan' &&
                in_array($verifikasi->kondisi, ['rusak_berat', 'hilang'])) {
                // Opsional: Kirim notifikasi ke user
                // Opsional: Buat laporan kerusakan otomatis
            }

            return redirect()
                ->route('backend.verifikasi.laporan.peminjaman')
                ->with('success', 'Tindakan berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error update tindakan verifikasi peminjaman', [
                'verifikasi_id' => $id,
                'error'         => $e->getMessage(),
            ]);

            return back()
                ->with('error', 'Gagal menyimpan tindakan: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * LAPORAN VERIFIKASI BOOKING RUANGAN
     * ==========================================
     */

    /**
     * Tampilkan semua laporan verifikasi booking dari PIC
     */
    public function laporanBooking(Request $request)
    {
        $query = VerifikasiBooking::with(['booking.ruangan', 'booking.user', 'pic']);

        // Filter by kondisi_ruangan  (select name="kondisi")
        if ($request->filled('kondisi')) {
            $query->where('kondisi_ruangan', $request->kondisi);
        }

        // Filter by status_verifikasi  (select name="status")
        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        // Date range  (tanggal_dari / tanggal_sampai → tanggal_verifikasi)
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_verifikasi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_verifikasi', '<=', $request->tanggal_sampai);
        }

        $verifikasi = $query->orderBy('tanggal_verifikasi', 'desc')->paginate(15);

        // Statistics — semua key yang dipakai di view
        $stats = [
            'total'          => VerifikasiBooking::count(),
            'baik'           => VerifikasiBooking::where('kondisi_ruangan', 'baik')->count(),
            'ruangan_kotor'  => VerifikasiBooking::where('kondisi_ruangan', 'kotor')->count(),
            'ruangan_rusak'  => VerifikasiBooking::where('kondisi_ruangan', 'rusak')->count(),
            'perlu_tindakan' => VerifikasiBooking::where('kondisi_ruangan', 'rusak')
                ->where('status_verifikasi', '!=', 'diterima')
                ->count(),
        ];

        return view('backend.verifikasi.laporan-booking', compact('verifikasi', 'stats'));
    }

    /**
     * Detail laporan verifikasi booking
     */
    public function detailBooking($id)
    {
        $verifikasi = VerifikasiBooking::with([
            'booking.user',
            'booking.ruangan',
            'pic',
        ])->findOrFail($id);

        return view('backend.verifikasi.detail-booking', compact('verifikasi'));
    }

    /**
     * Admin input tindakan untuk laporan booking
     */
    public function tindakanBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'tindakan_admin'    => 'required|string|max:1000',
            'status_verifikasi' => 'required|in:pending,diterima,perlu_tindakan',
        ], [
            'tindakan_admin.required'    => 'Keterangan tindakan harus diisi.',
            'status_verifikasi.required' => 'Status verifikasi harus dipilih.',
        ]);

        try {
            DB::beginTransaction();

            $verifikasi = VerifikasiBooking::findOrFail($id);

            $verifikasi->update([
                'tindakan_admin'    => $validated['tindakan_admin'],
                'status_verifikasi' => $validated['status_verifikasi'],
            ]);

            DB::commit();

            Log::info('Admin Update Tindakan Verifikasi Booking', [
                'verifikasi_id' => $id,
                'admin_id'      => auth()->id(),
                'tindakan'      => $validated['tindakan_admin'],
                'status'        => $validated['status_verifikasi'],
            ]);

            return back()->with('success', 'Tindakan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error update tindakan verifikasi booking', [
                'verifikasi_id' => $id,
                'error'         => $e->getMessage(),
            ]);

            return back()->with('error', 'Gagal menyimpan tindakan: ' . $e->getMessage());
        }
    }

    /**
     * ==========================================
     * EXPORT PDF LAPORAN
     * ==========================================
     */

    /**
     * Export PDF Laporan Peminjaman
     */
    public function exportPeminjaman(Request $request)
    {
        $query = VerifikasiPeminjaman::with([
            'peminjaman.user',
            'peminjaman.detailbarangs.barangRuangan.barang',
            'pic',
        ])->orderBy('created_at', 'DESC');

        // Aplikasikan filter yang sama
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        $laporan = $query->get();

        $pdf = \PDF::loadView('backend.verifikasi.pdf-laporan-peminjaman', compact('laporan'));

        return $pdf->download('Laporan-Verifikasi-Peminjaman-' . now()->format('d-m-Y') . '.pdf');
    }

    /**
     * Export PDF Laporan Booking
     */
    public function exportBooking(Request $request)
    {
        $query = VerifikasiBooking::with([
            'booking.user',
            'booking.ruangan',
            'pic',
        ])->orderBy('created_at', 'DESC');

        // Aplikasikan filter yang sama
        if ($request->filled('kondisi')) {
            $query->where('kondisi_ruangan', $request->kondisi);
        }
        if ($request->filled('status')) {
            $query->where('status_verifikasi', $request->status);
        }

        $laporan = $query->get();

        $pdf = \PDF::loadView('backend.verifikasi.pdf-laporan-booking', compact('laporan'));

        return $pdf->download('Laporan-Verifikasi-Booking-' . now()->format('d-m-Y') . '.pdf');
    }
}
