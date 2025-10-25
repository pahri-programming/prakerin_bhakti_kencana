<?php

namespace App\Events;

use App\Models\booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BookingStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;    

    public function __construct(booking $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn()
    {
        return new Channel('booking-channel');
    }

    public function broadcastAs()
    {
        return 'booking.updated';
    }
}
