<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ruangan extends Model
{
    protected $fillable = ['nama_ruangan', 'kapasitas', 'lokasi', 'status'];
    

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
    public function barangRuangan()
    {
        return $this->hasMany(BarangRuangan::class);
    }

}
