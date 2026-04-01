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
        'foto_bukti'           => 'array', // Multiple photos support
    ];

    // ==========================================
    // RELATIONHIPS
    // ==========================================

    /**
     * Relasi ke Pengembalian Barang
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

    /**
     * Relasi ke Denda (one)
     */
    public function denda()
    {
        return $this->hasOne(DendaPengembalian::class);
    }

    // ==========================================
    // ACCESSORS (Display Formatting)
    // ==========================================

    /**
     * Format tanggal verifikasi untuk display
     */
    public function getTanggalVerifikasiFormatAttribute(): string
    {
       return Carbon::parse($this->tanggal_verifikasi)->translatedFormat('d F Y, H:i') . ' WIB';
    }

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
     * Get status verifikasi label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_verifikasi) {
            'pending'        => '⏳ Menunggu Tindakan Admin',
            'diterima'       => '✅ Diterima',
            'perlu_tindakan' => '🚨 Perlu Tindakan Lanjut',
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
     * Get foto bukti URLs (array - multiple photos)
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

        // Backward compatibility: single upload (string)
        return [asset('storage/' . $this->foto_bukti)];
    }

    /**
     * Get foto bukti URL (first photo - backward compatibility)
     */
    public function getFotoBuktiUrlAttribute(): ?string
    {
        $urls = $this->foto_bukti_urls;
        return ! empty($urls) ? $urls[0] : null;
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

    // ==========================================
    // HELPER METHODS - Verifikasi
    // ==========================================

    /**
     * Check apakah ada masalah dengan barang
     */
    public function isProblematic(): bool
    {
        return in_array($this->kondisi, ['rusak_ringan', 'rusak_berat', 'hilang']);
    }

    /**
     * Check apakah perlu tindakan admin segera
     */
    public function needsAdminAction(): bool
    {
        return in_array($this->kondisi, ['rusak_berat', 'hilang']) &&
        $this->status_verifikasi === 'pending';
    }

    /**
     * Check apakah admin sudah memberi respone
     */
    public function hasAdminRespone(): bool
    {
        return ! empty($this->tindakan_admin);
    }

    /**
     * Check apakah punya multiple photos
     */
    public function hasMultiplePhotos(): bool
    {
        return is_array($this->foto_bukti) && count($this->foto_bukti) > 1;
    }

    // ==========================================
    // HELPER METHODS - Denda
    // ==========================================

    /**
     * Check apakah butuh denda (kondisi rusak/hilang)
     */
    public function needsDenda()
    {
        return in_array($this->kondisi, ['rusak_ringan', 'rusak_berat', 'hilang']);
    }

    /**
     * Check apakah sudah ada denda
     */
    public function hasDenda()
    {
        return $this->denda()->exists();
    }

    // ==========================================
    // SCOPES (Query Shortcuts)
    // ==========================================

    /**
     * Scope: Verifikasi yang perlu tindakan admin
     */
    public function scopeNeedsAction($query)
    {
        return $query->whereIn('kondisi', ['rusak_berat', 'hilang'])
            ->where('status_verifikasi', '!=', 'diterima');
    }

    /**
     * Scope: Verifikasi bermasalah (rusak/hilang)
     */
    public function scopeProblematic($query)
    {
        return $query->whereIn('kondisi', ['rusak_ringan', 'rusak_berat', 'hilang']);
    }

    /**
     * Scope: Filter by PIC
     */
    public function scopeByPic($query, $picId)
    {
        return $query->where('pic_id', $picId);
    }

    /**
     * Scope: Filter by status verifikasi
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_verifikasi', $status);
    }
}
