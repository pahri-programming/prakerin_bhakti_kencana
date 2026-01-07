<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs'; // WAJIB!

    protected $fillable = [
        'nama',
        'kategori_id',
        'keterangan',
    ];

    public function peminjaman()
    {
        return $this->hasMany(PeminjamanBarang::class);
    }

    public function barangruangan()
    {
        return $this->hasMany(BarangRuangan::class);
    }

    public function ruangan()
    {
        return $this->belongsToMany(Ruangan::class, 'barang_ruangans', 'barang_id', 'ruangan_id')
            ->withpivot('qty', 'status')
            ->withTimestamps();
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
