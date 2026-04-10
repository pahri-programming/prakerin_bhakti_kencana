<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DendaPengembalian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pengembalian_barang_id',
        'verifikasi_pengembalian_id',
        'jumlah_denda',
        'tipe_perhitungan',
        'rincian_perhitungan',
        'status_pembayaran',
        'tanggal_bayar',
        'bukti_pembayaran',
        'keterangan_pembayaran',
        'keterangan_denda',
        'tindakan_admin',
        'tanggal_tindakan',
        'admin_id',
        'verifikator_bayar_id',
    ];

    protected $casts = [
        'rincian_perhitungan' => 'array',
        'tanggal_bayar'       => 'datetime',
        'tanggal_tindakan'    => 'datetime',
        'jumlah_denda'        => 'decimal:2',
    ];

    /**
     * Relasi ke PengembalianBarang
     */
    public function pengembalianBarang()
    {
        return $this->belongsTo(PengembalianBarang::class);
    }

    /**
     * Relasi ke VerifikasiPengembalian
     */
    public function verifikasiPengembalian()
    {
        return $this->belongsTo(Verifikasipengembalian::class);
    }

    /**
     * Relasi ke Admin yang input denda
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relasi ke Admin yang verifikasi pembayaran
     */
    public function verifikatorBayar()
    {
        return $this->belongsTo(User::class, 'verifikator_bayar_id');
    }

    /**
     * Accessor: Format jumlah denda
     */
    public function getJumlahDendaFormatAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_denda, 0, ',', '.');
    }

    /**
     * Accessor: Status pembayaran badge class
     */
    public function getStatusPembayaranBadgeAttribute()
    {
        return [
            'belum_bayar' => 'bg-danger',
            'sudah_bayar' => 'bg-success',
            'menunggu_verifikasi' => 'bg-warning',
            'dibebaskan'  => 'bg-info',
        ][$this->status_pembayaran] ?? 'bg-secondary';
    }

    /**
     * Accessor: Status pembayaran label
     */
    public function getStatusPembayaranLabelAttribute()
    {
        return [
            'belum_bayar' => 'Belum Bayar',
            'sudah_bayar' => 'Sudah Bayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'dibebaskan'  => 'Dibebaskan',
        ][$this->status_pembayaran] ?? 'Unknown';
    }

    /**
     * Accessor: Tipe perhitungan label
     */
    public function getTipePerhitunganLabelAttribute()
    {
        return $this->tipe_perhitungan === 'otomatis' ? 'Otomatis' : 'Manual';
    }

    /**
     * Check apakah sudah dibayar
     */
    public function isBayar()
    {
        return $this->status_pembayaran === 'sudah_bayar';
    }

    /**
     * Check apakah belum dibayar
     */
    public function isBelumBayar()
    {
        return $this->status_pembayaran === 'belum_bayar';
    }

    /**
     * Check apakah dibebaskan
     */
    public function isDibebaskan()
    {
        return $this->status_pembayaran === 'dibebaskan';
    }

    /**
     * Scope: Belum bayar
     */
    public function scopeBelumBayar($query)
    {
        return $query->where('status_pembayaran', 'belum_bayar');
    }

    /**
     * Scope: Sudah bayar
     */
    public function scopeSudahBayar($query)
    {
        return $query->where('status_pembayaran', 'sudah_bayar');
    }

    /**
     * Scope: Dibebaskan
     */
    public function scopeDibebaskan($query)
    {
        return $query->where('status_pembayaran', 'dibebaskan');
    }

    // Scope baru
    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status_pembayaran', 'menunggu_verifikasi');
    }
}
