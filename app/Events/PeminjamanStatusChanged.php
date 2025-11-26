<?php
namespace App\Events;

use App\Models\PeminjamanBarang;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PeminjamanStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $status;
    public $barang;
    public $jumlah;
    /**
     * Create a new event instance.
     */

    public function __construct(PeminjamanBarang $p, $oldStatus)
    {
        $this->id     = $p->id;
        $this->status = $p->status;
        $this->barang = $p->barang->nama ?? '-';
        $this->jumlah = $p->jumlah;

    }

    // channel publik
    public function broadcastOn()
    {
        return new Channel('peminjaman');
    }

    // event name yg dipakai Echo.listen()
    public function broadcastAs()
    {
        return 'PeminjamanStatusChanged';
    }
}
