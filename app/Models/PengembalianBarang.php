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

    // ACCESSORS

    /**
     * Format tanggal kembali untuk display
     */
    public function getTanggalKembaliFormatAttribute()
    {
        return Carbon::parse($this->tanggal_kembali)->translatedFormat('d F Y');
    }

    /**
     * ✅ UPDATED: Get ringkasan status awal (bukan kondisi detail)
     * Admin cek: baik atau bermasalah
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
     * Get status label
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
     * Get status badge class
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

    // RELATIONSHIPS

    /**
     * Relasi ke PeminjamanBarang
     */
    public function peminjamanBarang()
    {
        return $this->belongsTo(PeminjamanBarang::class, 'peminjaman_barang_id');
    }

    /**
     * Relasi ke BarangRuangan
     */
    public function barangRuangan()
    {
        return $this->belongsTo(BarangRuangan::class, 'barang_ruangan_id');
    }

    /**
     * Relasi ke DetailPengembalianBarang (many)
     */
    public function detailpengembalians()
    {
        return $this->hasMany(DetailPengembalianBarang::class, 'pengembalian_barang_id');
    }

    /**
     * ✅ NEW: Relasi ke VerifikasiPengembalian (PIC verifikasi)
     */
    public function verifikasi()
    {
        return $this->hasOne(VerifikasiPengembalian::class, 'pengembalian_barang_id');
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

    // BOOT METHOD

    protected static function boot()
    {
        parent::boot();

        // Auto set tanggal kembali ke hari ini jika tidak diisi
        static::creating(function ($pengembalian) {
            if (empty($pengembalian->tanggal_kembali)) {
                $pengembalian->tanggal_kembali = now()->toDateString();
            }
        });
    }

    // HELPER METHODS

    /**
     * Check apakah semua barang sudah dikembalikan (status final)
     */
    public function isFullyReturned()
    {
        return $this->status === 'dikembalikan';
    }

    /**
     * ✅ NEW: Check apakah ada barang bermasalah (status awal admin)
     */
    public function hasProblematicItems()
    {
        return $this->detailpengembalians()
            ->where('status_awal', 'bermasalah')
            ->exists();
    }

    /**
     * ✅ NEW: Check apakah sudah diverifikasi oleh PIC
     */
    public function isVerified(): bool
    {
        return $this->verifikasi()->exists();
    }

    /**
     * ✅ NEW: Check apakah perlu verifikasi PIC
     * (Ada barang bermasalah tapi belum diverifikasi)
     */
    public function needsVerification(): bool
    {
        return $this->hasProblematicItems() && ! $this->isVerified();
    }

    /**
     * ✅ NEW: Check apakah menunggu PIC
     */
    public function isWaitingForPic(): bool
    {
        return $this->status === 'menunggu_pic';
    }

    /**
     * ✅ NEW: Check apakah perlu tindakan admin
     */
    public function needsAdminAction(): bool
    {
        return $this->status === 'perlu_tindakan';
    }

    /**
     * Hitung total barang yang dikembalikan
     */
    public function getTotalItemsAttribute()
    {
        return $this->detailpengembalians->sum('jumlah');
    }

    // SCOPES

    /**
     * Scope untuk pengembalian yang menunggu verifikasi PIC
     */
    public function scopeWaitingForPic($query)
    {
        return $query->where('status', 'menunggu_pic');
    }

    /**
     * Scope untuk pengembalian yang perlu tindakan admin
     */
    public function scopeNeedsAction($query)
    {
        return $query->where('status', 'perlu_tindakan');
    }

    /**
     * Scope untuk pengembalian yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'dikembalikan');
    }
}
