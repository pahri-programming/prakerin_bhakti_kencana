<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    // ===========================
    // ACCESSORS
    // ===========================

    /**
     * Format tanggal kembali untuk display
     */
    public function getTanggalKembaliFormatAttribute()
    {
        return Carbon::parse($this->tanggal_kembali)->translatedFormat('d F Y');
    }

    /**
     * Get ringkasan kondisi barang
     */
    public function getKondisiSummaryAttribute()
    {
        $baik = $this->detailpengembalians->where('kondisi', 'baik')->count();
        $rusak = $this->detailpengembalians->where('kondisi', 'rusak')->count();
        $hilang = $this->detailpengembalians->where('kondisi', 'hilang')->count();

        $summary = [];
        if ($baik > 0) $summary[] = "{$baik} baik";
        if ($rusak > 0) $summary[] = "{$rusak} rusak";
        if ($hilang > 0) $summary[] = "{$hilang} hilang";

        return implode(', ', $summary) ?: 'Tidak ada data';
    }

    // ===========================
    // RELATIONSHIPS
    // ===========================

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
     * Relasi ke User melalui peminjaman
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            PeminjamanBarang::class,
            'id',                      // Foreign key di peminjaman_barangs
            'id',                      // Foreign key di users
            'peminjaman_barang_id',    // Local key di pengembalian_barangs
            'user_id'                  // Local key di peminjaman_barangs
        );
    }

    // ===========================
    // BOOT METHOD
    // ===========================

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

    // ===========================
    // HELPER METHODS
    // ===========================

    /**
     * Check apakah semua barang sudah dikembalikan
     */
    public function isFullyReturned()
    {
        return $this->status === 'dikembalikan';
    }

    /**
     * Check apakah ada barang yang rusak atau hilang
     */
    public function hasDamagedItems()
    {
        return $this->detailpengembalians()
            ->whereIn('kondisi', ['rusak', 'hilang'])
            ->exists();
    }

    /**
     * Hitung total barang yang dikembalikan
     */
    public function getTotalItemsAttribute()
    {
        return $this->detailpengembalians->sum('jumlah');
    }
}