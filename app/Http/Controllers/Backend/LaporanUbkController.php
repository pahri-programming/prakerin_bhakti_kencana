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

        $bookings         = collect();
        $peminjamans      = collect();
        $total_booking    = 0;
        $total_peminjaman = 0;

        // Update status otomatis berdasarkan tanggal dan waktu
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
                $q->where('tanggal', '<', now()->toDateString())
                    ->orWhere(function ($s) {
                        $s->where('tanggal', now()->toDateString())
                            ->where('waktu_selesai', '<', now()->format('H:i:s'));
                    });
            })
            ->update(['status' => 'selesai']);

        if ($jenis === 'booking' || ! $jenis) {
            $query = Booking::with(['user', 'ruangan'])
                ->when($start && $end, fn($q) => $q->whereBetween('tanggal', [$start, $end]))
                ->whereIn('status', ['Pending', 'Diterima', 'Selesai']);

            $bookings      = $query->latest()->get()->map(fn($d) => $this->formatBooking($d));
            $total_booking = $bookings->unique('nama')->count();
        }

        if ($jenis === 'peminjaman' || ! $jenis) {
            $query = PeminjamanBarang::with(['user', 'barang'])
                ->when($start && $end, fn($q) => $q->whereBetween('tanggal', [$start, $end]))
                ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam', 'selesai']);

            $peminjamans      = $query->latest()->get()->map(fn($d) => $this->formatPeminjaman($d));
            $total_peminjaman = $peminjamans->unique('nama')->count();
        }

        // simpan data di session untuk generate PDF
        if ($jenis) {
            $data  = $jenis === 'booking' ? $bookings : $peminjamans;
            $total = $jenis === 'booking' ? $total_booking : $total_peminjaman;
            $judul = $jenis === 'booking' ? 'Laporan Booking Ruangan' : 'Laporan Peminjaman Barang';

            session([
                'laporan_data'    => $data,
                'laporan_total'   => $total,
                'laporan_judul'   => $judul,
                'laporan_periode' => $start && $end
                    ? Carbon::parse($start)->translatedFormat('d M Y') . ' - ' . Carbon::parse($end)->translatedFormat('d M Y')
                    : 'Semua Periode',
                'laporan_jenis'   => $jenis,
            ]);
        }

        return view('backend.laporan-ubk.index', compact(
            'bookings', 'peminjamans', 'total_booking', 'total_peminjaman', 'jenis'
        ));
    }

    private function formatBooking($d)
    {
        return (object) [
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
            'nama'              => $d->user?->name ?? 'User Dihapus',
            'item'              => $d->barang?->nama ?? 'Barang Dihapus',
            'jumlah'            => $d->jumlah ?? 1,
            'tanggal_indonesia' => Carbon::parse($d->tanggal)->translatedFormat('d F Y'),
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

    public function pdf(Request $request)
    {
        // Kalau session belum ada, jalankan ulang index biar data masuk
        if (! session('laporan_data')) {
            $this->index($request);
        }

        $data    = session('laporan_data');
        $total   = session('laporan_total');
        $judul   = session('laporan_judul');
        $periode = session('laporan_periode');

        $pdf = Pdf::loadView('backend.laporan-ubk.pdf', compact(
            'data', 'total', 'judul', 'periode'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("UBK_{$judul}_" . now()->format('d-m-Y') . '.pdf');
    }

    // public function pdf(Request $request)
    // {
    //     if (! session('laporan_jenis')) {
    //         $this->index($request);
    //     }

    //     $jenis    = session('laporan_jenis');
    //     $bookings = session('laporan_booking');
    //     $pinjam   = session('laporan_pinjam');
    //     $total    = session('laporan_total');
    //     $judul    = session('laporan_judul');
    //     $periode  = session('laporan_periode');

    //     $pdf = Pdf::loadView('backend.laporan-ubk.pdf', compact(
    //         'jenis', 'bookings', 'pinjam', 'total', 'judul', 'periode'
    //     ))->setPaper('a4', 'landscape');

    //     return $pdf->download("UBK_{$judul}_" . now()->format('d-m-Y') . '.pdf');
    // }

}
