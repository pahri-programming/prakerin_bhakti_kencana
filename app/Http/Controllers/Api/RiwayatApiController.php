<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PeminjamanBarang;
use Illuminate\Http\Request;

class RiwayatApiController extends Controller
{
    /**
     * GET /api/riwayat
     * Semua riwayat booking + peminjaman milik user
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Peminjaman
        $peminjaman = PeminjamanBarang::with([
            'detailbarangs.barangRuangan.barang',
            'detailbarangs.barangRuangan.ruangan',
        ])
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn($p) => $this->formatPeminjaman($p));

        // Booking
        $booking = Booking::with('ruangan')
            ->where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn($b) => $this->formatBooking($b));

        return response()->json([
            'success'    => true,
            'peminjaman' => $peminjaman,
            'booking'    => $booking,
        ], 200);
    }

    private function formatPeminjaman($p)
    {
        return [
            'id'              => $p->id,
            'type'            => 'peminjaman',
            'kode'            => $p->kode,
            'status'          => $p->status,
            'alasan_tolak'    => $p->alasan_tolak,
            'tanggal_pinjam'  => $p->tanggal_pinjam,
            'tanggal_kembali' => $p->tanggal_kembali,
            'created_at'      => $p->created_at,
            'barang'          => $p->detailbarangs->map(fn($d) => [
                'nama_barang'  => $d->barangRuangan->barang->nama ?? '-',
                'nama_ruangan' => $d->barangRuangan->ruangan->nama_ruangan ?? '-',
                'jumlah'       => $d->jumlah,
            ]),
        ];
    }

    private function formatBooking($b)
    {
        return [
            'id'            => $b->id,
            'type'          => 'booking',
            'kode'          => $b->kode,
            'status'        => $b->status,
            'keterangan'    => $b->keterangan,
            'tanggal'       => $b->tanggal,
            'waktu_mulai'   => $b->waktu_mulai,
            'waktu_selesai' => $b->waktu_selesai,
            'created_at'    => $b->created_at,
            'ruangan'       => [
                'nama_ruangan' => $b->ruangan->nama_ruangan ?? '-',
                'lokasi'       => $b->ruangan->lokasi ?? null,
            ],
        ];
    }
}
