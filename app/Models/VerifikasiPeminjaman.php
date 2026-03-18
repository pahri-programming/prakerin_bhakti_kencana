<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_peminjaman';

    protected $fillable = [
        'peminjaman_id',
        'pic_id',
        'kondisi',
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
        'foto_bukti'           => 'array',
    ];

    //  RELATIONSHIPS

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanBarang::class, 'peminjaman_id');
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    //  ACCESSORS

    public function getKondisiLabelAttribute(): string
    {
        return match ($this->kondisi) {
            'baik'         => '✅ Baik',
            'rusak_ringan' => '⚠️ Rusak Ringan',
            'rusak_berat'  => '🔴 Rusak Berat',
            'hilang'       => '❌ Hilang',
            default        => 'Unknown',
        };
    }


    public function getKondisiBadgeAttribute(): string
    {
        return match ($this->kondisi) {
            'baik'         => 'success',
            'rusak_ringan' => 'warning',
            'rusak_berat'  => 'danger',
            'hilang'       => 'dark',
            default        => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'pending'        => 'Menunggu Tindakan Admin',
            'diterima'       => 'Diterima',
            'perlu_tindakan' => 'Perlu Tindakan Lanjut',
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

     public function getFotoBuktiUrlAttribute(): ?string
    {
        $urls = $this->foto_bukti_urls;
        return !empty($urls) ? $urls[0] : null;
    }

    /**
     * Check if has multiple photos
     */
    public function hasMultiplePhotos(): bool
    {
        return is_array($this->foto_bukti) && count($this->foto_bukti) > 1;
    }

    /**
     * Get total photos count
     */
    public function getTotalPhotosAttribute(): int
    {
        if (!$this->foto_bukti) {
            return 0;
        }
        
        return is_array($this->foto_bukti) ? count($this->foto_bukti) : 1;
    }

    public function getTanggalVerifikasiFormatAttribute(): string
    {
        return Carbon::parse($this->tanggal_verifikasi)->translatedFormat('d F Y, H:i') . ' WIB';
    }

    //  HELPER METHODS

    public function getFotoBuktiUrlsAttribute(): array
    {
        if (! $this->foto_bukti) {
            return [];
        }

        // Jika foto_bukti adalah array (multiple upload)
        if (is_array($this->foto_bukti)) {
            return array_map(function ($path) {
                return asset('storage/' . $path);
            }, $this->foto_bukti);
        }

        // Backward compatibility: jika foto_bukti masih string (single upload lama)
        return [asset('storage/' . $this->foto_bukti)];
    }

    

    public function needsAdminAction(): bool
    {
        return in_array($this->kondisi, ['rusak_berat', 'hilang']) &&
        $this->status_verifikasi === 'pending';
    }

    public function isProblematic(): bool
    {
        return in_array($this->kondisi, ['rusak_ringan', 'rusak_berat', 'hilang']);
    }

    // Relasi ke Verifikasi PIC
    public function verifikasi()
    {
        return $this->hasOne(VerifikasiPeminjaman::class, 'peminjaman_id');
    }

    // Cek apakah sudah diverifikasi oleh PIC
    public function isVerified(): bool
    {
        return $this->verifikasi()->exists();
    }

    // Cek apakah perlu verifikasi (status dikembalikan tapi belum diverifikasi)
    public function needsVerification(): bool
    {
        return $this->status === 'dikembalikan' && ! $this->isVerified();
    }

    // Accessor untuk status badge
    public function getStatusBadgeVerifikasiAttribute(): string
    {
        if ($this->isVerified()) {
            return 'success';
        } elseif ($this->needsVerification()) {
            return 'warning';
        }
        return 'secondary';
    }
}
