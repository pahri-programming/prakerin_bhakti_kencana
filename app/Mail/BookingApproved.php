<?php
namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Ruangan Anda Diterima!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking.approved',
            with: ['booking' => $this->booking]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
