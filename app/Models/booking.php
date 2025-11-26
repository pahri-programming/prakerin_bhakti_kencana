<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class booking extends Model
{
    protected $fillable = ['kode', 'user_id', 'ruang_id', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'status', 'keterangan', 'is_read'];

    public function getTanggalFormatAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal)->translatedFormat('d F Y');
    }

    protected $casts = [
        'is_read' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {

            // Format tanggal: 20251105
            $today = now()->format('Ymd');

            // Generate random unique 8 karakter
            $unique = strtoupper(Str::random(8));

            // Final kode: BOOK-20251105-ABC123XY
            $booking->kode = "BOOK-{$today}-{$unique}";
        });

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'ruang_id');
    }
}
