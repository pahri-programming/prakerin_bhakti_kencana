<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'foto',
        'nama',
        'kode',
        'kategori_id',
        'stok',
        'keterangan'
    ];

    public function peminjaman()
    {
        return $this->hasMany(PeminjamanBarang::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
