<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanBarang extends Model
{
    protected $fillable = [
        'user_id', 'barang_id', 'jumlah', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'status', 'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
