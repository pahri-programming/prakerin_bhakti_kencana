<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DendaBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'verifikasi_booking_id',
        'jumlah_denda',
        'keterangan_denda',
        'tindakan_admin',
        'status_pembayaran',
        'bukti_pembayaran',
        'tanggal_bayar',
        'keterangan_pembayaran',
        'tanggal_tindakan',
        'admin_id',
        'verifikator_bayar_id',
    ];

    protected $casts = [
        'tanggal_bayar'    => 'datetime',
        'tanggal_tindakan' => 'datetime',
        'jumlah_denda'     => 'decimal:2',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function booking()
    {
        return $this->belongsTo(booking::class);
    }

    public function verifikasiBooking()
    {
        return $this->belongsTo(VerifikasiBooking::class, 'verifikasi_booking_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function verifikatorBayar()
    {
        return $this->belongsTo(User::class, 'verifikator_bayar_id');
    }

    // ── Accessors ────────────────────────────────────────

    public function getJumlahDendaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_denda, 0, ',', '.');
    }

    public function getStatusPembayaranBadgeAttribute(): string
    {
        return [
            'belum_bayar'         => 'bg-danger',
            'menunggu_verifikasi' => 'bg-warning',
            'sudah_bayar'         => 'bg-success',
            'dibebaskan'          => 'bg-secondary',
        ][$this->status_pembayaran] ?? 'bg-secondary';
    }

    public function getStatusPembayaranLabelAttribute(): string
    {
        return [
            'belum_bayar'         => 'Belum Bayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'sudah_bayar'         => 'Sudah Bayar',
            'dibebaskan'          => 'Dibebaskan',
        ][$this->status_pembayaran] ?? 'Unknown';
    }

    // ── Helpers ──────────────────────────────────────────

    public function isBayar(): bool
    {
        return $this->status_pembayaran === 'sudah_bayar';
    }

    public function isBelumBayar(): bool
    {
        return $this->status_pembayaran === 'belum_bayar';
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status_pembayaran === 'menunggu_verifikasi';
    }

    public function isDibebaskan(): bool
    {
        return $this->status_pembayaran === 'dibebaskan';
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeBelumBayar($query)
    {
        return $query->where('status_pembayaran', 'belum_bayar');
    }

    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status_pembayaran', 'menunggu_verifikasi');
    }

    public function scopeSudahBayar($query)
    {
        return $query->where('status_pembayaran', 'sudah_bayar');
    }
}
