<?php
namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class BookingExpired implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('ruangan', 'user');

    }

    public function broadcastOn(): Channel
    {
        return new Channel('booking-expired');
    }

    public function broadcastAs()
    {
        return 'BookingExpired';
    }

    public function broadcastWith()
    {
        return [
            'id'         => $this->booking->id,
            'kode'       => $this->booking->kode ?? null,
            'ruang_nama' => $this->booking->ruangan?->nama_ruangan ?? null,
            'user_name'  => $this->booking->user?->name ?? null,
            'expired_at' => now()->format('Y-m-d H:i:s'),
        ];

    }
}
