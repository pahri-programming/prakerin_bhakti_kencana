<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PeminjamanBarang extends Model
{
    protected $table = 'peminjaman_barangs';

    protected $fillable = [
        'kode',
        'user_id',
        'barang_id',
        'nama_peminjam',
        'instansi',
        'jumlah',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'keterangan',
        'is_read',
    ];

    protected $casts = [
        'tanggal_pinjam'  => 'date:Y-m-d',
        'tanggal_kembali' => 'date:Y-m-d',
        'status'          => 'string',
        'is_read'         => 'boolean',
    ];

    // Accessors
    public function getTanggalPinjamFormatAttribute()
    {
        return Carbon::parse($this->tanggal_pinjam)->translatedFormat('d F Y');
    }

    public function getTanggalKembaliFormatAttribute()
    {
        return Carbon::parse($this->tanggal_kembali)->translatedFormat('d F Y');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function detailbarangs()
    {
        return $this->hasMany(DetailPeminjamanBarang::class, 'peminjaman_barang_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pinjam) {
            $today        = now()->format('Ymd');
            $unique       = strtoupper(Str::random(6));
            $pinjam->kode = "PINJ-{$today}-{$unique}";
        });

        // // Perubahan stok saat status berubah — hati-hati race condition
        // static::updating(function ($pinjam) {
        //     // $oldStatus = $pinjam->getOriginal('status');
        //     // $newStatus = $pinjam->status;

        //     // if (! $pinjam->barang) {
        //     //     return;
        //     // }

        //     // $barang = $pinjam->barang;
        //     // $jumlah = (int) $pinjam->jumlah;

        //     // if ($oldStatus === 'menunggu' && $newStatus === 'disetujui') {
        //     //     if ($barang->stok < $jumlah) {
        //     //         throw new \Exception("Stok tidak mencukupi: tersedia {$barang->stok}, diminta {$jumlah}");
        //     //     }
        //     //     $barang->decrement('stok', $jumlah);
        //     // }

        //     // if (in_array($oldStatus, ['disetujui', 'dipinjam']) && in_array($newStatus, ['selesai', 'ditolak', 'dikembalikan'])) {
        //     //     $barang->increment('stok', $jumlah);
        //     // }
        // });
    }
}
