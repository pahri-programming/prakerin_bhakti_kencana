<?php
namespace App\Models;

use App\Models\BarangRuangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PeminjamanBarang extends Model
{
    protected $table = 'peminjaman_barangs';

    protected $fillable = [
        'kode',
        'user_id',
        'nama_peminjam',
        'instansi',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'alasan_tolak',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pinjam'  => 'date:Y-m-d',
        'tanggal_kembali' => 'date:Y-m-d',
        'status'          => 'string',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Relasi ke User (peminjam)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Detail Peminjaman (many)
     */
    public function detailbarangs()
    {
        return $this->hasMany(DetailPeminjamanBarang::class, 'peminjaman_barang_id');
    }

    /**
     * Relasi ke Pengembalian (many) - untuk history
     */
    public function pengembalianbarangs()
    {
        return $this->hasMany(PengembalianBarang::class, 'peminjaman_barang_id');
    }

    public function details()
    {
        return $this->hasMany(DetailPeminjamanBarang::class, 'peminjaman_barang_id');
    }

    /**
     * Relasi ke Pengembalian (one) - untuk current return
     */
    public function pengembalian()
    {
        return $this->hasOne(PengembalianBarang::class, 'peminjaman_barang_id');
    }

    /**
     * Relasi ke Verifikasi Peminjaman (OPSI 2 - jika dipakai)
     */
    public function verifikasi()
    {
        return $this->hasOne(VerifikasiPeminjaman::class, 'peminjaman_id');
    }

    // ==========================================
    // ACCESSORS (Display Formatting)
    // ==========================================

    /**
     * Format tanggal pinjam untuk display
     */
    public function getTanggalPinjamFormatAttribute()
    {
        return Carbon::parse($this->tanggal_pinjam)->translatedFormat('d F Y');
    }

    /**
     * Format tanggal kembali untuk display
     */
    public function getTanggalKembaliFormatAttribute()
    {
        return Carbon::parse($this->tanggal_kembali)->translatedFormat('d F Y');
    }

    /**
     * Ringkasan barang untuk dashboard
     * Example: "Laptop Asus dan 2 lainnya"
     */
    public function getBarangSummaryAttribute()
    {
        $count = $this->detailbarangs->count();

        if ($count === 0) {
            return 'Tidak ada barang';
        }

        $first     = $this->detailbarangs->first();
        $firstName = $first->barangRuangan->barang->nama ?? '-';

        if ($count > 1) {
            return "{$firstName} dan " . ($count - 1) . " lainnya";
        }

        return $firstName;
    }

    /**
     * Total jumlah barang (semua detail dijumlahkan)
     */
    public function getTotalJumlahAttribute()
    {
        return $this->detailbarangs->sum('jumlah');
    }

    // ==========================================
    // HELPER METHODS - Pengembalian
    // ==========================================

    /**
     * Check apakah sudah pernah dikembalikan
     */
    public function hasReturn()
    {
        return $this->pengembalianbarangs()->exists();
    }

    /**
     * Check apakah sudah dikembalikan dengan status final
     */
    public function isReturned()
    {
        return $this->pengembalianbarangs()
            ->where('status', 'dikembalikan')
            ->exists();
    }

    // ==========================================
    // HELPER METHODS - Verifikasi (OPSI 2)
    // ==========================================

    /**
     * Check apakah sudah diverifikasi oleh PIC
     */
    public function isVerified(): bool
    {
        return $this->verifikasi()->exists();
    }

    /**
     * Check apakah perlu verifikasi
     */
    public function needsVerification(): bool
    {
        return $this->status === 'dikembalikan' && ! $this->isVerified();
    }

    /**
     * Get status badge class untuk verifikasi
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
            $kondisi = $this->verifikasi->kondisi ?? 'unknown';
            return match ($kondisi) {
                'baik'         => '✅ Baik (Verified)',
                'rusak_ringan' => '⚠️ Rusak Ringan',
                'rusak_berat'  => '🔴 Rusak Berat',
                'hilang'       => '❌ Hilang',
                default        => 'Sudah Diverifikasi',
            };
        } elseif ($this->needsVerification()) {
            return 'Perlu Verifikasi PIC';
        }
        return '-';
    }

    // ==========================================
    // BOOT (Auto-generate kode)
    // ==========================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pinjam) {
            if (empty($pinjam->kode)) {
                $today        = now()->format('Ymd');
                $unique       = strtoupper(Str::random(6));
                $pinjam->kode = "PINJ-{$today}-{$unique}";
            }
        });
    }
}
