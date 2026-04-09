<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PengembalianBarang extends Model
{
    protected $table = 'pengembalian_barangs';

    protected $fillable = [
        'peminjaman_barang_id',
        'barang_ruangan_id',
        'tanggal_kembali',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kembali' => 'date:Y-m-d',
        'status'          => 'string',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Relasi ke Peminjaman Barang
     */
    public function peminjamanBarang()
    {
        return $this->belongsTo(PeminjamanBarang::class, 'peminjaman_barang_id');
    }

    /**
     * Relasi ke Barang Ruangan
     */
    public function barangRuangan()
    {
        return $this->belongsTo(BarangRuangan::class, 'barang_ruangan_id');
    }

    /**
     * Relasi ke Detail Pengembalian (many)
     */
    public function detailpengembalians()
    {
        return $this->hasMany(DetailPengembalianBarang::class, 'pengembalian_barang_id');
    }

    /**
     * Relasi ke Verifikasi PIC (one) - OPSI 1
     */
    public function verifikasi()
    {
        return $this->hasOne(Verifikasipengembalian::class, 'pengembalian_barang_id');
    }

    /**
     * Relasi ke Denda (one) - Sistem Denda
     */
    public function denda()
    {
        return $this->hasOne(DendaPengembalian::class);
    }

    /**
     * Relasi ke User melalui peminjaman
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            PeminjamanBarang::class,
            'id',                   // Foreign key di peminjaman_barangs
            'id',                   // Foreign key di users
            'peminjaman_barang_id', // Local key di pengembalian_barangs
            'user_id'               // Local key di peminjaman_barangs
        );
    }

    // ==========================================
    // ACCESSORS (Display Formatting)
    // ==========================================

    /**
     * Format tanggal kembali untuk display
     */
    public function getTanggalKembaliFormatAttribute()
    {
        return Carbon::parse($this->tanggal_kembali)->translatedFormat('d F Y');
    }

    /**
     * Ringkasan status awal barang (admin check)
     * Example: "2 baik, 1 bermasalah"
     */
    public function getStatusAwalSummaryAttribute()
    {
        $baik       = $this->detailpengembalians->where('status_awal', 'baik')->count();
        $bermasalah = $this->detailpengembalians->where('status_awal', 'bermasalah')->count();

        $summary = [];
        if ($baik > 0) {
            $summary[] = "{$baik} baik";
        }
        if ($bermasalah > 0) {
            $summary[] = "{$bermasalah} bermasalah";
        }

        return implode(', ', $summary) ?: 'Tidak ada data';
    }

    /**
     * Get status label (readable)
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_pic'   => '⏳ Menunggu Verifikasi PIC',
            'dikembalikan'   => '✅ Dikembalikan',
            'perlu_tindakan' => '🚨 Perlu Tindakan Admin',
            default          => 'Unknown',
        };
    }

    /**
     * Get status badge class (untuk UI)
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'menunggu_pic'   => 'info',
            'dikembalikan'   => 'success',
            'perlu_tindakan' => 'danger',
            default          => 'secondary',
        };
    }

    /**
     * Total barang yang dikembalikan
     */
    public function getTotalItemsAttribute()
    {
        return $this->detailpengembalians->sum('jumlah');
    }

    // ==========================================
    // HELPER METHODS - Verifikasi
    // ==========================================

    /**
     * Check apakah ada barang bermasalah
     */
    public function hasProblematicItems()
    {
        return $this->detailpengembalians()
            ->where('status_awal', 'bermasalah')
            ->exists();
    }

    /**
     * Check apakah sudah diverifikasi oleh PIC
     */
    public function isVerified(): bool
    {
        return $this->verifikasi()->exists();
    }

    /**
     * Check apakah perlu verifikasi PIC
     */
    public function needsVerification(): bool
    {
        return $this->hasProblematicItems() && !$this->isVerified();
    }

    /**
     * Check apakah menunggu PIC
     */
    public function isWaitingForPic(): bool
    {
        return $this->status === 'menunggu_pic';
    }

    /**
     * Check apakah perlu tindakan admin
     */
    public function needsAdminAction(): bool
    {
        return $this->status === 'perlu_tindakan';
    }

    /**
     * Check apakah sudah selesai dikembalikan
     */
    public function isFullyReturned()
    {
        return $this->status === 'dikembalikan';
    }

    // ==========================================
    // HELPER METHODS - Denda
    // ==========================================

    /**
     * Check apakah ada denda
     */
    public function hasDenda()
    {
        return $this->denda()->exists();
    }

    /**
     * Check apakah denda sudah dibayar
     */
    public function isDendaBayar()
    {
        return $this->hasDenda() && $this->denda->isBayar();
    }

    /**
     * Get total denda (return 0 jika tidak ada)
     */
    public function getTotalDenda()
    {
        return $this->hasDenda() ? $this->denda->jumlah_denda : 0;
    }

    // ==========================================
    // SCOPES (Query Shortcuts)
    // ==========================================

    /**
     * Scope: Pengembalian yang menunggu verifikasi PIC
     */
    public function scopeWaitingForPic($query)
    {
        return $query->where('status', 'menunggu_pic');
    }

    /**
     * Scope: Pengembalian yang perlu tindakan admin
     */
    public function scopeNeedsAction($query)
    {
        return $query->where('status', 'perlu_tindakan');
    }

    /**
     * Scope: Pengembalian yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'dikembalikan');
    }

    // ==========================================
    // BOOT (Auto-fill defaults)
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        // Auto set tanggal kembali ke hari ini jika kosong
        static::creating(function ($pengembalian) {
            if (empty($pengembalian->tanggal_kembali)) {
                $pengembalian->tanggal_kembali = now()->toDateString();
            }
        });
    }
}