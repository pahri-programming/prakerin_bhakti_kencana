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

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruang_id');
    }


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
                    $adaBookingAktif = Booking::where('ruang_id', $booking->ruang_id)
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
                $adaBookingAktif = Booking::where('ruang_id', $booking->ruang_id)
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
