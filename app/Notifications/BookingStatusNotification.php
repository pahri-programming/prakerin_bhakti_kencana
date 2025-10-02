<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\booking;

class BookingStatusNotification extends Notification
{
    use Queueable;

   
   protected $booking;

    public function __construct($booking)
    {
    $this->booking = $booking;

    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
     public function via($notifiable)
    {
        return ['database']; // simpan ke tabel notifications
    }

    public function toDatabase($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'status'     => $this->booking->status,
            'ruangan'    => $this->booking->ruangan->nama_ruangan,
            'tanggal'    => $this->booking->tanggal,
            'mulai'      => $this->booking->waktu_mulai,
            'selesai'    => $this->booking->waktu_selesai,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
