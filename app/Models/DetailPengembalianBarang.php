<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPengembalianBarang extends Model
{
    protected $table = 'detail_pengembalian_barangs';

    protected $fillable = [
        'pengembalian_barang_id',
        'barang_id',
        'jumlah',
        'kondisi',
    ];

    protected $casts = [
        'jumlah'  => 'integer',
        'kondisi' => 'string',
    ];

    /**
     * Get kondisi badge class untuk UI
     */
    public function getKondisiBadgeClassAttribute()
    {
        return match ($this->kondisi) {
            'baik'   => 'success',
            'rusak'  => 'warning',
            'hilang' => 'danger',
            default  => 'secondary',
        };
    }

    /**
     * Get kondisi label untuk display
     */
    public function getKondisiLabelAttribute()
    {
        return match ($this->kondisi) {
            'baik'   => 'Baik',
            'rusak'  => 'Rusak',
            'hilang' => 'Hilang',
            default  => 'Tidak Diketahui',
        };
    }

    /**
     * Relasi ke PengembalianBarang
     */
    public function pengembalianBarang()
    {
        return $this->belongsTo(PengembalianBarang::class, 'pengembalian_barang_id');
    }

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Check apakah barang dalam kondisi baik
     */
    public function isGoodCondition()
    {
        return $this->kondisi === 'baik';
    }

    /**
     * Check apakah barang rusak
     */
    public function isDamaged()
    {
        return $this->kondisi === 'rusak';
    }

    /**
     * Check apakah barang hilang
     */
    public function isLost()
    {
        return $this->kondisi === 'hilang';
    }
}
