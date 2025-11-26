<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
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
        
        // otomatis update status booking dan peminjaman yang sudah lewat
        Booking::where('status', 'Diterima')
            ->where(function ($q) {
                $q->where('tanggal', '<', now()->toDateString())
                    ->orWhere(function ($s) {
                        $s->where('tanggal', now()->toDateString())
                            ->where('waktu_selesai', '<', now()->format('H:i:s'));
                    });
            })
            ->update(['status' => 'Selesai']);

        PeminjamanBarang::whereIn('status', ['dipinjam', 'disetujui'])
            ->where(function ($q) {
                $q->where('tanggal_kembali', '<', now()->toDateString())
                    ->orWhere(function ($s) {
                        $s->where('tanggal_kembali', now()->toDateString())
                            ->where('waktu_selesai', '<', now()->format('H:i:s'));
                    });
            })
            ->update(['status' => 'selesai']);

        // booking query
        if ($isBooking) {
            $query = Booking::with(['user', 'ruangan'])
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
            $query = PeminjamanBarang::with(['user', 'barang'])
                ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam', 'selesai']);

            if ($start && $end) {
                // overlap logic (dipertahankan)
                $query->where(function ($q) use ($start, $end) {
                    $q->where('tanggal_pinjam', '<=', $end)
                        ->where('tanggal_kembali', '>=', $start);
                });
            }

            $peminjamans = $query
                ->orderByRaw('COALESCE(tanggal_kembali, tanggal_pinjam) DESC')
                ->get()
                ->map(fn($d) => $this->formatPeminjaman($d));

            $total_peminjaman = $peminjamans->unique('nama')->count();
        }

        
        //penyimpanan data di session untuk laporan PDF
        if ($jenis) {
            $data  = $isBooking ? $bookings : $peminjamans;
            $total = $isBooking ? $total_booking : $total_peminjaman;
            $judul = $isBooking ? 'Laporan Booking Ruangan' : 'Laporan Peminjaman Barang';

            session([
                'laporan_data'    => $data,
                'laporan_total'   => $total,
                'laporan_judul'   => $judul,
                'laporan_periode' => ($start && $end)
                    ? Carbon::parse($start)->translatedFormat('d M Y') . ' - ' . Carbon::parse($end)->translatedFormat('d M Y')
                    : 'Semua Periode',
                'laporan_jenis'   => $jenis,
            ]);
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
        return (object) [
            'kode'              => $d->kode,
            'nama'              => $d->user?->name ?? 'User Dihapus',
            'item'              => $d->ruangan?->nama_ruangan ?? 'Ruangan Dihapus',
            'tanggal_indonesia' => Carbon::parse($d->tanggal)->translatedFormat('d F Y'),
            'waktu'             => "{$d->waktu_mulai} - {$d->waktu_selesai}",
            'status_laporan' => match ($d->status) {
                'Pending'  => 'Menunggu',
                'Diterima' => 'Disetujui',
                'Selesai'  => 'Selesai',
                default    => 'Unknown',
            },
        ];
    }

    private function formatPeminjaman($d)
    {
        return (object) [
            'kode'              => $d->kode,
            'nama'              => $d->user?->name ?? 'User Dihapus',
            'item'              => $d->barang?->nama ?? 'Barang Dihapus',
            'jumlah'            => $d->jumlah ?? 1,
            'tanggal_indonesia' =>
            Carbon::parse($d->tanggal_pinjam)->translatedFormat('d F Y')
            . ' - ' .
            Carbon::parse($d->tanggal_kembali)->translatedFormat('d F Y'),
            'waktu'             => "{$d->waktu_mulai} - {$d->waktu_selesai}",
            'status' => strtolower($d->status),
            'status_laporan' => match (strtolower($d->status)) {
                'menunggu'  => 'Menunggu',
                'disetujui' => 'Disetujui',
                'dipinjam'  => 'Dipinjam',
                'selesai'   => 'Selesai',
                default     => 'Unknown',
            },
            'keterangan' => $d->keterangan ?? '-',
        ];
    }

    public function pdfBooking(Request $request)
    {
        if (! session('laporan_jenis') || session('laporan_jenis') !== 'booking') {
            $this->index($request);
        }

        $data    = session('laporan_data');
        $total   = session('laporan_total');
        $judul   = session('laporan_judul');
        $periode = session('laporan_periode');

        $pdf = Pdf::loadView('backend.laporan-ubk.pdf_booking', compact(
            'data', 'total', 'judul', 'periode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download(
            "BKU-LaporanBooking-" . Carbon::now()->locale('id')->translatedFormat('d-m-Y') . '.pdf'
        );

    }

    public function pdfPeminjaman(Request $request)
    {
        if (! session('laporan_jenis') || session('laporan_jenis') !== 'peminjaman') {
            $this->index($request);
        }

        $data    = session('laporan_data');
        $total   = session('laporan_total');
        $judul   = session('laporan_judul');
        $periode = session('laporan_periode');

        $pdf = Pdf::loadView('backend.laporan-ubk.pdf_peminjaman', compact(
            'data', 'total', 'judul', 'periode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download(
            "BKU-LaporanPeminjaman-" . Carbon::now()->locale('id')->translatedFormat('d-m-Y') . '.pdf'
        );

    }

}
