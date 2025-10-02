<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ruangan extends Model
{
    protected $fillable = ['cover', 'kode_ruangan', 'nama_ruangan', 'kapasitas', 'lokasi', 'fasilitas'];

    public function booking()
    {
        return $this->hasMany(Booking::class, 'ruang_id');
        // kasih tau kalo foreign key di tabel bookings itu ruang_id
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'ruang_id');
        // sama juga untuk tabel jadwal
    }

}
