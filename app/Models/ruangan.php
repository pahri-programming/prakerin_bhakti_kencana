<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{

    protected $fillable = [
        'nama_ruangan',
        'kapasitas',
        'lokasi',
        'status',
    ];

    // Relasi
    public function booking()
    {
        return $this->hasMany(Booking::class, 'ruang_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'ruang_id');
    }

    public function barangRuangan()
    {
        return $this->hasMany(BarangRuangan::class);
    }

    //  Helper Method: Cek apakah ruangan sedang dipinjam
    public function isTersedia()
    {
        return $this->status === 'tersedia';
    }

    public function isDipinjam()
    {
        return $this->status === 'dipinjam';
    }

    //  Helper Method: Get booking aktif (status: Diterima)
    public function getBookingAktifAttribute()
    {
        return $this->booking()
            ->where('status', 'Diterima')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();
    }

    //  Helper Method: Cek apakah ruangan available di tanggal & waktu tertentu
    public function isAvailableAt($tanggal, $waktuMulai, $waktuSelesai, $excludeBookingId = null)
    {
        $query = $this->booking()
            ->where('tanggal', $tanggal)
            ->where('status', 'Diterima')
            ->where(function ($q) use ($waktuMulai, $waktuSelesai) {
                $q->whereBetween('waktu_mulai', [$waktuMulai, $waktuSelesai])
                    ->orWhereBetween('waktu_selesai', [$waktuMulai, $waktuSelesai])
                    ->orWhere(function ($q2) use ($waktuMulai, $waktuSelesai) {
                        $q2->where('waktu_mulai', '<=', $waktuMulai)
                            ->where('waktu_selesai', '>=', $waktuSelesai);
                    });
            });

        // Exclude booking tertentu (untuk edit booking)
        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return ! $query->exists();
    }

    //  Scope: Filter ruangan tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDipinjam($query)
    {
        return $query->where('status', 'dipinjam');
    }
}
