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
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pinjam'  => 'date:Y-m-d',
        'tanggal_kembali' => 'date:Y-m-d',
        'status'          => 'string',
    ];

    // Accessors untuk format tanggal
    public function getTanggalPinjamFormatAttribute()
    {
        return Carbon::parse($this->tanggal_pinjam)->translatedFormat('d F Y');
    }

    public function getTanggalKembaliFormatAttribute()
    {
        return Carbon::parse($this->tanggal_kembali)->translatedFormat('d F Y');
    }

    //  Accessor untuk ringkasan barang (dashboard)
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

    //  Accessor untuk total jumlah barang
    public function getTotalJumlahAttribute()
    {
        return $this->detailbarangs->sum('jumlah');
    }

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailbarangs()
    {
        return $this->hasMany(DetailPeminjamanBarang::class, 'peminjaman_barang_id');
    }

    public function pengembalianbarangs()
    {
        return $this->hasMany(PengembalianBarang::class, 'peminjaman_barang_id');
    }

    public function hasReturn()
    {
        return $this->pengembalianbarangs()->exists();
    }

    public function isReturned()
    {
        return $this->pengembalianbarangs()
            ->where('status', 'dikembalikan')
            ->exists();
    }

    // Boot method untuk auto generate kode
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
