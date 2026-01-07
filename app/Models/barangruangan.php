<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class barangruangan extends Model
{
    protected $table = 'barang_ruangans';

    protected $fillable = [
        'barang_id',
        'ruangan_id',
        'qty',
        'status',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
