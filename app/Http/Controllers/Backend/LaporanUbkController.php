<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\booking;
use App\Models\PeminjamanBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanUbkController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis');
        $start = $request->get('start_date');
        $end   = $request->get('end_date');

        $isBooking    = $jenis === 'booking';
        $isPeminjaman = $jenis === 'peminjaman';

        $bookings         = collect();
        $peminjamans      = collect();
        $total_booking    = 0;
        $total_peminjaman = 0;

        // otomatis update status booking yang sudah lewat
       booking::where('status', 'Diterima')
            ->where(function ($q) {
                $q->where('tanggal', '<', now()->toDateString())
                    ->orWhere(function ($s) {
                        $s->where('tanggal', now()->toDateString())
                            ->where('waktu_selesai', '<', now()->format('H:i:s'));
                    });
            })
            ->update(['status' => 'Selesai']);

        // otomatis update status peminjaman yang sudah lewat tanggal kembali
        PeminjamanBarang::whereIn('status', ['dipinjam', 'disetujui'])
            ->where('tanggal_kembali', '<', now()->toDateString())
            ->update(['status' => 'dikembalikan']);

        // booking query
        if ($isBooking) {
            $query =booking::with(['user', 'ruangan'])
                ->whereIn('status', ['Pending', 'Diterima', 'Selesai']);

            if ($start && $end) {
                $query->whereBetween('tanggal', [$start, $end]);
            }

            $bookings = $query
                ->orderBy('tanggal', 'desc')
                ->get()
                ->map(fn($d) => $this->formatBooking($d));

            $total_booking = $bookings->unique('nama')->count();
        }

        // peminjaman query
        if ($isPeminjaman) {
            $query = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan'])
                ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam', 'dikembalikan']);

            if ($start && $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('tanggal_pinjam', '<=', $end)
                        ->where('tanggal_kembali', '>=', $start);
                });
            }

            $peminjamans = $query
                ->orderBy('tanggal_pinjam', 'desc')
                ->get();

            $total_peminjaman = $peminjamans->unique('user_id')->count();
        }

        return view('backend.laporan-ubk.index', compact(
            'bookings',
            'peminjamans',
            'total_booking',
            'total_peminjaman',
            'jenis',
            'isBooking',
            'isPeminjaman'
        ));
    }

    private function formatBooking($d)
    {
        $tanggal = $d->tanggal ?? now()->toDateString();

        return (object) [
            'kode'              => $d->kode ?? 'N/A',
            'nama'              => $d->user?->name ?? 'User Dihapus',
            'item'              => $d->ruangan?->nama_ruangan ?? 'Ruangan Dihapus',
            'tanggal_indonesia' => Carbon::parse($tanggal)->translatedFormat('d F Y'),
            'hari'              => Carbon::parse($tanggal)->translatedFormat('l'),
            'waktu'             => ($d->waktu_mulai ?? '00:00') . ' - ' . ($d->waktu_selesai ?? '00:00'),
            'status_laporan'    => match ($d->status ?? 'Unknown') {
                'Pending'  => 'Menunggu',
                'Diterima' => 'Disetujui',
                'Selesai'  => 'Selesai',
                'Ditolak'  => 'Ditolak',
                default    => 'Unknown',
            },
        ];
    }

    public function pdfBooking(Request $request)
    {
        $start = $request->get('start_date');
        $end   = $request->get('end_date');

        $query =booking::with(['user', 'ruangan'])
            ->whereIn('status', ['Pending', 'Diterima', 'Selesai']);

        if ($start && $end) {
            $query->whereBetween('tanggal', [$start, $end]);
        }

        $bookings = $query
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($d) => $this->formatBooking($d));

        $data    = $bookings;
        $total   = $bookings->unique('nama')->count();
        $judul   = 'Laporan Booking Ruangan';
        $periode = ($start && $end)
            ? Carbon::parse($start)->translatedFormat('d M Y') . ' - ' . Carbon::parse($end)->translatedFormat('d M Y')
            : 'Semua Periode';

        $pdf = Pdf::loadView('backend.laporan-ubk.pdf_booking', compact(
            'data', 'total', 'judul', 'periode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download(
            "BKU-LaporanBooking-" . Carbon::now()->locale('id')->translatedFormat('d-m-Y') . '.pdf'
        );
    }

    public function pdfPeminjaman(Request $request)
    {
        $start = $request->get('start_date');
        $end   = $request->get('end_date');

        $query = PeminjamanBarang::with(['user', 'detailbarangs.barangRuangan.barang', 'detailbarangs.barangRuangan.ruangan'])
            ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam', 'dikembalikan']);

        if ($start && $end) {
            $query->where(function ($q) use ($start, $end) {
                $q->where('tanggal_pinjam', '<=', $end)
                    ->where('tanggal_kembali', '>=', $start);
            });
        }

        $peminjamans = $query
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();

        $total   = $peminjamans->unique('user_id')->count();
        $judul   = 'Laporan Peminjaman Barang';
        $periode = ($start && $end)
            ? Carbon::parse($start)->translatedFormat('d M Y') . ' - ' . Carbon::parse($end)->translatedFormat('d M Y')
            : 'Semua Periode';

        $pdf = Pdf::loadView('backend.laporan-ubk.pdf_peminjaman', compact(
            'peminjamans', 'total', 'judul', 'periode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download(
            "BKU-LaporanPeminjaman-" . Carbon::now()->locale('id')->translatedFormat('d-m-Y') . '.pdf'
        );
    }
}
