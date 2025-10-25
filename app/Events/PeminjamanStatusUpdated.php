<?php

namespace App\Events;

use App\Models\PeminjamanBarang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PeminjamanStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $peminjaman;

    public function __construct(PeminjamanBarang $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function broadcastOn()
    {
        // channel publik (bisa disesuaikan jadi private kalau pakai auth)
        return new Channel('peminjaman-channel');
    }

    public function broadcastAs()
    {
        return 'peminjaman.updated';
    }
}
