<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class booking extends Model
{
    protected $fillable = ['user_id', 'ruang_id', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'ruang_id');
    }
}
