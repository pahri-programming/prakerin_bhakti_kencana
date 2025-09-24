<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jadwal extends Model
{
    protected $fillable = ['ruang_id', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'kegiatan'];

    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'ruang_id');
    }
}
