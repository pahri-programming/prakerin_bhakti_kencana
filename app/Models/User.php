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

    // ================= ROLE HELPER METHODS =================

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

    // ================= RELASI =================

    public function booking()
    {
        return $this->hasMany(Booking::class);
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

    // 🔥 RELASI VERIFIKASI (UNTUK PIC) - TAMBAHAN BARU

    /**
     * Verifikasi Peminjaman yang dilakukan oleh PIC ini
     */
    public function verifikasiPeminjaman()
    {
        return $this->hasMany(VerifikasiPeminjaman::class, 'pic_id');
    }

    /**
     * Verifikasi Booking yang dilakukan oleh PIC ini
     */
    public function verifikasiBooking()
    {
        return $this->hasMany(VerifikasiBooking::class, 'pic_id');
    }

    // ================= HELPER METHODS UNTUK PIC =================

    /**
     * Total verifikasi yang dilakukan hari ini (untuk PIC)
     */
    public function getTotalVerifikasiTodayAttribute(): int
    {
        if (! $this->isPIC()) {
            return 0;
        }

        $peminjaman = $this->verifikasiPeminjaman()
            ->whereDate('created_at', today())
            ->count();

        $booking = $this->verifikasiBooking()
            ->whereDate('created_at', today())
            ->count();

        return $peminjaman + $booking;
    }

    /**
     * Total verifikasi bulan ini (untuk PIC)
     */
    public function getTotalVerifikasiMonthAttribute(): int
    {
        if (! $this->isPIC()) {
            return 0;
        }

        $peminjaman = $this->verifikasiPeminjaman()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $booking = $this->verifikasiBooking()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $peminjaman + $booking;
    }

    /**
     * Total verifikasi dengan kondisi bermasalah (untuk PIC)
     */
    public function getTotalVerifikasiProblematicAttribute(): int
    {
        if (! $this->isPIC()) {
            return 0;
        }

        $peminjaman = $this->verifikasiPeminjaman()
            ->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang'])
            ->count();

        $booking = $this->verifikasiBooking()
            ->whereIn('kondisi_ruangan', ['kotor', 'rusak'])
            ->count();

        return $peminjaman + $booking;
    }
}
