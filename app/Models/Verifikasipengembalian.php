<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiPengembalian extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_pengembalian';

    protected $fillable = [
        'pengembalian_barang_id',
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
        'foto_bukti'           => 'array', // Cast ke array untuk multiple photos
    ];

    // RELATIONSHIPS 
    /**
     * Relasi ke PengembalianBarang
     */
    public function pengembalianBarang()
    {
        return $this->belongsTo(PengembalianBarang::class, 'pengembalian_barang_id');
    }

    /**
     * Relasi ke PIC (User)
     */
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    // ACCESSORS 
    /**
     * Get kondisi label dengan emoji
     */
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

    /**
     * Get kondisi badge class untuk UI
     */
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

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'pending'        => 'Menunggu Tindakan Admin',
            'diterima'       => 'Diterima',
            'perlu_tindakan' => 'Perlu Tindakan Lanjut',
            default          => 'Unknown',
        };
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'pending'        => 'warning',
            'diterima'       => 'success',
            'perlu_tindakan' => 'danger',
            default          => 'secondary',
        };
    }

    /**
     * Get foto bukti URL array (multiple photos)
     */
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

    /**
     * Get foto bukti URL (first photo only - backward compatibility)
     */
    public function getFotoBuktiUrlAttribute(): ?string
    {
        $urls = $this->foto_bukti_urls;
        return ! empty($urls) ? $urls[0] : null;
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
        if (! $this->foto_bukti) {
            return 0;
        }

        return is_array($this->foto_bukti) ? count($this->foto_bukti) : 1;
    }

    /**
     * Format tanggal verifikasi
     */
    public function getTanggalVerifikasiFormatAttribute(): string
    {
        return Carbon::parse($this->tanggal_verifikasi)->translatedFormat('d F Y, H:i') . ' WIB';
    }

    // HELPER METHODS 
    /**
     * Check apakah perlu tindakan admin segera
     */
    public function needsAdminAction(): bool
    {
        return in_array($this->kondisi, ['rusak_berat', 'hilang']) &&
        $this->status_verifikasi === 'pending';
    }

    /**
     * Check apakah ada masalah dengan barang
     */
    public function isProblematic(): bool
    {
        return in_array($this->kondisi, ['rusak_ringan', 'rusak_berat', 'hilang']);
    }

    /**
     * Check apakah admin sudah memberi response
     */
    public function hasAdminResponse(): bool
    {
        return ! empty($this->tindakan_admin);
    }

    // SCOPES 
    /**
     * Scope untuk verifikasi yang perlu tindakan admin
     */
    public function scopeNeedsAction($query)
    {
        return $query->whereIn('kondisi', ['rusak_berat', 'hilang'])
            ->where('status_verifikasi', '!=', 'diterima');
    }

    /**
     * Scope untuk verifikasi bermasalah
     */
    public function scopeProblematic($query)
    {
        return $query->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang']);
    }

    /**
     * Scope berdasarkan PIC
     */
    public function scopeByPic($query, $picId)
    {
        return $query->where('pic_id', $picId);
    }

    /**
     * Scope berdasarkan status verifikasi
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_verifikasi', $status);
    }
}
