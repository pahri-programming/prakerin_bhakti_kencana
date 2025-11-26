<?php
namespace App\Listeners;

use App\Events\BookingStatusChanged;
use App\Mail\BookingApproved;
use App\Mail\BookingRejected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingStatusEmail
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingStatusChanged $event): void
    {
        $booking   = $event->booking;
        $newStatus = $booking->status;
        $oldStatus = $event->oldStatus;

        if ($newStatus === $oldStatus) {
            return;
        }

        $mailable = match ($newStatus) {
            'Diterima' => new BookingApproved($booking),
            'Ditolak'  => new BookingRejected($booking),
            'Selesai'  => new BookingCompleted($booking),
            default    => null,
        };

        if ($mailable) {
            Mail::to($booking->user->email)->queue($mailable);
        }
    }
}
