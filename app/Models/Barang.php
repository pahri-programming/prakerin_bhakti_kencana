<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'kategori',
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
