<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'kode',
        'user_id',
        'ruang_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Accessor untuk format tanggal Indonesia
    public function getTanggalFormatAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    // = RELASI =

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Pengembalian Ruangan
     */
    public function pengembalian()
    {
        return $this->hasOne(PengembalianRuangan::class, 'booking_id');
    }

    /**
     * Check apakah sudah ada pengembalian
     */
    public function hasPengembalian()
    {
        return $this->pengembalian()->exists();
    }

    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'ruang_id');
    }

    //  RELASI KE VERIFIKASI PIC (TAMBAHAN BARU)
    public function verifikasi()
    {
        return $this->hasOne(\App\Models\VerifikasiBooking::class, 'booking_id');
    }

    // = HELPER METHODS VERIFIKASI (TAMBAHAN BARU) =

    /**
     * Cek apakah sudah diverifikasi oleh PIC
     */
    public function isVerified(): bool
    {
        return $this->verifikasi()->exists();
    }

    /**
     * Cek apakah perlu verifikasi
     * (status Selesai tapi belum diverifikasi)
     */
    public function needsVerification(): bool
    {
        return $this->status === 'Selesai' && ! $this->isVerified();
    }

    public function denda()
    {
        return $this->hasOne(DendaBooking::class);
    }

    public function hasDenda(): bool
    {
        return $this->denda()->exists();
    }

    /**
     * Get status badge untuk verifikasi
     */
    public function getStatusVerifikasiBadgeAttribute(): string
    {
        if ($this->isVerified()) {
            return 'success';
        } elseif ($this->needsVerification()) {
            return 'warning';
        }
        return 'secondary';
    }

    /**
     * Get label status verifikasi
     */
    public function getStatusVerifikasiLabelAttribute(): string
    {
        if ($this->isVerified()) {
            $kondisi = $this->verifikasi->kondisi_ruangan ?? 'unknown';
            return match ($kondisi) {
                'baik'  => '✅ Baik & Bersih',
                'kotor' => '🧹 Kotor/Perlu Bersih',
                'rusak' => '🔴 Rusak',
                default => 'Sudah Diverifikasi',
            };
        } elseif ($this->needsVerification()) {
            return 'Perlu Verifikasi PIC';
        }
        return '-';
    }

    // = BOOT METHOD =

    protected static function boot()
    {
        parent::boot();

        // 🔹 Auto-generate kode booking saat create
        static::creating(function ($booking) {
            $today         = now()->format('Ymd');
            $unique        = strtoupper(Str::random(8));
            $booking->kode = "BOOK-{$today}-{$unique}";
        });

        // 🔹 Auto-update status ruangan saat booking di-update
        static::updated(function ($booking) {
            // Cek apakah field 'status' yang berubah
            if ($booking->isDirty('status')) {
                $ruangan = $booking->ruangan;

                // Jika status booking jadi "Diterima"
                if ($booking->status === 'Diterima') {
                    // Set ruangan jadi "dipinjam"
                    $ruangan->update(['status' => 'dipinjam']);

                    Log::info("🟢 Ruangan '{$ruangan->nama_ruangan}' status: DIPINJAM (Booking #{$booking->kode} diterima)");
                }

                // Jika status booking jadi "Selesai", "Ditolak", atau "Pending"
                if (in_array($booking->status, ['Selesai', 'Ditolak', 'Pending'])) {
                    // Cek apakah masih ada booking lain dengan status "Diterima" di ruangan yang sama
                    $adaBookingAktif =booking::where('ruang_id', $booking->ruang_id)
                        ->where('id', '!=', $booking->id)
                        ->where('status', 'Diterima')
                        ->exists();

                    // Jika tidak ada booking aktif lain, set ruangan jadi "tersedia"
                    if (! $adaBookingAktif) {
                        $ruangan->update(['status' => 'tersedia']);

                        Log::info("🟢 Ruangan '{$ruangan->nama_ruangan}' status: TERSEDIA (Booking #{$booking->kode} {$booking->status})");
                    } else {
                        Log::info("⚠️ Ruangan '{$ruangan->nama_ruangan}' masih DIPINJAM (Ada booking aktif lain)");
                    }
                }
            }
        });

        // 🔹 Auto-update status ruangan saat booking dihapus
        static::deleted(function ($booking) {
            // Hanya proses jika booking yang dihapus statusnya "Diterima"
            if ($booking->status === 'Diterima') {
                $ruangan = $booking->ruangan;

                // Cek apakah masih ada booking lain dengan status "Diterima"
                $adaBookingAktif =booking::where('ruang_id', $booking->ruang_id)
                    ->where('status', 'Diterima')
                    ->exists();

                // Jika tidak ada, set ruangan jadi "tersedia"
                if (! $adaBookingAktif) {
                    $ruangan->update(['status' => 'tersedia']);

                    Log::info("🟢 Ruangan '{$ruangan->nama_ruangan}' status: TERSEDIA (Booking #{$booking->kode} dihapus)");
                }
            }
        });
    }
}
