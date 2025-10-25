<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function booking()
    {
        return $this->hasMany(booking::class);
    }

    // Relasi ke Peminjaman Barang
    public function peminjamanBarangs()
    {
        return $this->hasMany(PeminjamanBarang::class);
    }

    // Relasi ke Pelaporan Kerusakan (sebagai pelapor)
    public function pelaporanKerusakans()
    {
        return $this->hasMany(PelaporanKerusakan::class, 'user_id');
    }

    // Relasi ke Pelaporan Kerusakan (sebagai teknisi)
    public function laporanDitangani()
    {
        return $this->hasMany(PelaporanKerusakan::class, 'teknisi_id');
    }

}
