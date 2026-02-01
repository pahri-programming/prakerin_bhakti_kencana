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
        'instansi',
        'isAdmin',
        'role',
        'email_verified_at',
        'provider',
        'provider_id',

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
            'isAdmin'           => 'boolean',

        ];
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is PIC (Person In Charge)
     */
    public function isPIC(): bool
    {
        return $this->role === 'pic';
    }

    /**
     * Check if user is regular User
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Check if user is Admin or PIC (has access to backend)
     */
    public function hasBackendAccess(): bool
    {
        return in_array($this->role, ['admin', 'pic']);
    }

    /**
     * Get role label in Indonesian
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'pic'   => 'Petugas (PIC)',
            'user'  => 'User/Mahasiswa',
            default => 'Unknown',
        };
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

}
