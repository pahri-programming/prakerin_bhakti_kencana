<?php
namespace App\Services;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class StockService
{
    // Kurangi stok barang
    public function reduce($barangId, $jumlah)
    {
        return DB::transaction(function () use ($barangId, $jumlah) {
            $barang = Barang::lockForUpdate()->find($barangId);
            if (! $barang || $barang->stok < $jumlah) {
                return false;
            }

            $barang->decrement('stok', $jumlah);
            return true;
        });
    }

    // Tambah stok barang
    public function increase($barangId, $jumlah)
    {
        return DB::transaction(function () use ($barangId, $jumlah) {
            $barang = Barang::lockForUpdate()->find($barangId);
            if (! $barang) {
                return false;
            }

            $barang->increment('stok', $jumlah);
            return true;
        });
    }
}
