<?php
namespace App\Services;

use App\Models\Barang;
use App\Models\PeminjamanBarang;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Mengecek ketersediaan barang berdasarkan rentang waktu.
     */

    public function check($barangId, $tanggalPinjam, $tanggalKembali, $waktuMulai, $waktuSelesai, $excludeId = null)
    {
        // parse datetimes
        $start = Carbon::parse("$tanggalPinjam $waktuMulai");
        $end   = Carbon::parse("$tanggalKembali $waktuSelesai");

        if ($start->gte($end)) {
            return [
                'status'    => false,
                'available' => 0,
                'message'   => 'Waktu mulai harus lebih kecil dari waktu selesai.',
            ];
        }

        $barang = Barang::find($barangId);
        if (! $barang) {
            return [
                'status'    => false,
                'available' => 0,
                'message'   => 'Barang tidak ditemukan.',
            ];
        }

        $stokAwal = (int) $barang->stok;

        // hanya status yang benar-benar mengurangi stok
        $statusMengurangi = ['disetujui', 'dipinjam'];

        // gunakan CONCAT untuk membuat "datetime" di SQL dan bandingkan dengan string datetime terformat
        $startStr = $start->format('Y-m-d H:i:s');
        $endStr   = $end->format('Y-m-d H:i:s');

        $query = PeminjamanBarang::where('barang_id', $barangId)
            ->whereIn('status', $statusMengurangi)
            ->whereRaw("
            CONCAT(tanggal_pinjam, ' ', waktu_mulai) <= ?
            AND CONCAT(tanggal_kembali, ' ', waktu_selesai) >= ?
        ", [$endStr, $startStr]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $terpakai  = (int) $query->sum('jumlah');
        $available = max(0, $stokAwal - $terpakai);

        return [
            'status'    => $available > 0,
            'available' => $available,
            'message'   => $available > 0 ? "Stok tersedia: {$available}" : "Stok tidak mencukupi pada rentang waktu tersebut.",
        ];
    }
}
