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
        'status_awal', //  UPDATED: Ganti dari 'kondisi' ke 'status_awal'
    ];

    protected $casts = [
        'jumlah'      => 'integer',
        'status_awal' => 'string', //  UPDATED
    ];

    // ACCESSORS

    /**
     *  UPDATED: Get status awal badge class untuk UI
     * Admin input: baik atau bermasalah
     */
    public function getStatusAwalBadgeClassAttribute()
    {
        return match ($this->status_awal) {
            'baik'       => 'success',
            'bermasalah' => 'warning',
            default      => 'secondary',
        };
    }

    /**
     *  UPDATED: Get status awal label untuk display
     */
    public function getStatusAwalLabelAttribute()
    {
        return match ($this->status_awal) {
            'baik'       => '✅ Baik',
            'bermasalah' => '⚠️ Ada Masalah',
            default      => 'Tidak Diketahui',
        };
    }

    // RELATIONSHIPS

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

    // HELPER METHODS

    /**
     *  UPDATED: Check apakah status awal baik
     */
    public function isGoodCondition()
    {
        return $this->status_awal === 'baik';
    }

    /**
     *  UPDATED: Check apakah ada masalah
     */
    public function hasProblems()
    {
        return $this->status_awal === 'bermasalah';
    }

    /**
     *  NEW: Check apakah perlu verifikasi PIC
     */
    public function needsPicVerification()
    {
        return $this->hasProblems();
    }
}
