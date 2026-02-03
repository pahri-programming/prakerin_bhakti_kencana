<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiBooking extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_booking';

    protected $fillable = [
        'booking_id',
        'pic_id',
        'kondisi_ruangan',
        'catatan_pic',
        'foto_bukti',
        'status_verifikasi',
        'tindakan_admin',
        'tanggal_verifikasi',
        'is_reported_to_admin',
    ];

    protected $casts = [
        'tanggal_verifikasi'   => 'datetime',
        'is_reported_to_admin' => 'boolean',
    ];

    // ================= RELATIONSHIPS =================

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    // ================= ACCESSORS =================

    public function getKondisiLabelAttribute(): string
    {
        return match ($this->kondisi_ruangan) {
            'baik'  => '✅ Baik & Bersih',
            'kotor' => '🧹 Kotor / Perlu Dibersihkan',
            'rusak' => '🔴 Rusak / Butuh Perbaikan',
            default => 'Unknown',
        };
    }

    public function getKondisiBadgeAttribute(): string
    {
        return match ($this->kondisi_ruangan) {
            'baik'  => 'success',
            'kotor' => 'warning',
            'rusak' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'pending'        => '⏳ Menunggu Tindakan Admin',
            'diterima'       => '✅ Diterima',
            'perlu_tindakan' => '🔴 Perlu Tindakan Lanjut',
            default          => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'pending'        => 'warning',
            'diterima'       => 'success',
            'perlu_tindakan' => 'danger',
            default          => 'secondary',
        };
    }

    public function getTanggalVerifikasiFormatAttribute(): string
    {
        return Carbon::parse($this->tanggal_verifikasi)->translatedFormat('d F Y, H:i') . ' WIB';
    }

    public function getFotoBuktiUrlAttribute(): ?string
    {
        return $this->foto_bukti ? asset('storage/' . $this->foto_bukti) : null;
    }

    // ================= HELPER METHODS =================

    public function needsAdminAction(): bool
    {
        return $this->kondisi_ruangan === 'rusak' &&
        $this->status_verifikasi === 'pending';
    }

    public function isProblematic(): bool
    {
        return in_array($this->kondisi_ruangan, ['kotor', 'rusak']);
    }

    public function hasAdminResponse(): bool
    {
        return ! empty($this->tindakan_admin);
    }

    // ================= SCOPES =================

    public function scopeNeedsAction($query)
    {
        return $query->where('kondisi_ruangan', 'rusak')
            ->where('status_verifikasi', '!=', 'diterima');
    }

    public function scopeProblematic($query)
    {
        return $query->whereIn('kondisi_ruangan', ['kotor', 'rusak']);
    }

    public function scopeByPic($query, $picId)
    {
        return $query->where('pic_id', $picId);
    }
}
