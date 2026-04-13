<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jadwal extends Model
{
    protected $fillable = [
        'ruang_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'kegiatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi
    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'ruang_id');
    }

    // Accessor untuk format tanggal
    public function getTanggalFormatAttribute()
    {
        return $this->tanggal->translatedFormat('d F Y');
    }

    // Accessor untuk hari
    public function getHariAttribute()
    {
        return $this->tanggal->translatedFormat('l');
    }
}
