<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjamanBarang extends Model
{
    protected $table = 'detail_peminjaman_barangs';

    protected $fillable = [
        'peminjaman_barang_id',
        'barang_ruangan_id',
        'jumlah',
        'keterangan',
    ];

    public function peminjamanBarang()
    {
        return $this->belongsTo(PeminjamanBarang::class, 'peminjaman_barang_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function barangRuangan()
    {
        return $this->belongsTo(BarangRuangan::class, 'barang_ruangan_id');
    }

    // Helper accessor untuk akses langsung ke barang
    public function getBarangAttribute()
    {
        return $this->barangRuangan->barang ?? null;
    }

    // Helper accessor untuk akses langsung ke ruangan
    public function getRuanganAttribute()
    {
        return $this->barangRuangan->ruangan ?? null;
    }
}
