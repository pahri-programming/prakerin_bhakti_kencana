<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ruangan extends Model
{
    protected $fillable = ['cover', 'nama_ruangan', 'kapasitas', 'fasilitas'];

    public function booking()
    {
        return $this->hasMany(booking::class);
    }
 
    public function jadwal()
    {
        return $this->hasMany(jadwal::class);
    }
}
