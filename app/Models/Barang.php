<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'nama',
        'kategori_id',
        'harga',
        'keterangan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Relasi ke Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi ke Peminjaman Barang
     */
    public function peminjaman()
    {
        return $this->hasMany(PeminjamanBarang::class);
    }

    /**
     * Relasi ke Barang Ruangan
     */
    public function barangruangan()
    {
        return $this->hasMany(BarangRuangan::class);
    }

    /**
     * Relasi many-to-many ke Ruangan via barang_ruangans
     */
    public function ruangan()
    {
        return $this->belongsToMany(Ruangan::class, 'barang_ruangans', 'barang_id', 'ruangan_id')
            ->withPivot('qty', 'status')
            ->withTimestamps();
    }

    /**
     * Relasi ke Detail Pengembalian
     */
    public function detailPengembalians()
    {
        return $this->hasMany(DetailPengembalianBarang::class, 'barang_id');
    }

    // ==========================================
    // ACCESSORS (Display Formatting)
    // ==========================================

    /**
     * Format harga untuk display
     */
    public function getHargaFormatAttribute()
    {
        return $this->harga ? 'Rp ' . number_format($this->harga, 0, ',', '.') : 'Rp 0';
    }

    // ==========================================
    // COMPUTED ATTRIBUTES (Statistics)
    // ==========================================

    /**
     * Total barang yang dikembalikan
     */
    public function getTotalReturnedAttribute()
    {
        return $this->detailPengembalians()->sum('jumlah');
    }

    /**
     * Total barang yang rusak
     */
    public function getTotalDamagedAttribute()
    {
        return $this->detailPengembalians()
            ->where('status_awal', 'bermasalah')
            ->sum('jumlah');
    }

    /**
     * Total barang yang hilang (dari verifikasi)
     */
    public function getTotalLostAttribute()
    {
        return $this->detailPengembalians()
            ->whereHas('pengembalianBarang.verifikasi', function ($q) {
                $q->where('kondisi', 'hilang');
            })
            ->sum('jumlah');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Check apakah barang punya harga
     */
    public function hasPrice()
    {
        return $this->harga !== null && $this->harga > 0;
    }

    /**
     * Get suggested denda based on kondisi
     */
    public function getSuggestedDenda($kondisi, $jumlah = 1)
    {
        if (! $this->hasPrice()) {
            return 0;
        }

        $persentase = [
            'rusak_ringan' => 20,
            'rusak_berat'  => 80,
            'hilang'       => 100,
        ][$kondisi] ?? 0;

        return ($this->harga * $persentase / 100) * $jumlah;
    }
}
