<?php
namespace App\Events;

use App\Models\PeminjamanBarang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class PeminjamanExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $peminjaman;

    public function __construct(PeminjamanBarang $peminjaman)
    {
        $this->peminjaman = $peminjaman->load('barang', 'user');

    }

    public function broadcastOn()
    {
        return new Channel('peminjaman');
    }

    public function broadcastAs()
    {
        return 'PeminjamanExpired';
    }

    public function broadcastWith()
    {
        return [
            'id'        => $this->peminjaman->id,
            'kode'      => $this->peminjaman->kode ?? null,
            'barang'    => $this->peminjaman->barang?->nama ?? null,
            'jumlah'    => $this->peminjaman->jumlah,
            'user_name' => $this->peminjaman->user?->name ?? null,
            'status'    => $this->peminjaman->status,
            'expired_at'=> now()->format('Y-m-d H:i:s'),    
        ];

    }
}
