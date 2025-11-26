<?php
namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $oldStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, $oldStatus)
    {
        $this->booking   = $booking->load('ruangan', 'user');
        $this->oldStatus = $oldStatus;
    }

    public function broadcastAs()
    {
        return 'status.changed';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('booking.' . $this->booking->id);
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->booking->id,
            'kode'       => $this->booking->kode,
            'status'     => $this->booking->status,
            'old_status' => $this->oldStatus,
            'ruang'      => $this->booking->ruangan?->nama_ruangan,
            'user'       => $this->booking->user?->name,
            'waktu'      => $this->booking->waktu_mulai . ' - ' . $this->booking->waktu_selesai,
            'tanggal'    => $this->booking->tanggal_format ?? $this->booking->tanggal,
        ];
    }
}
