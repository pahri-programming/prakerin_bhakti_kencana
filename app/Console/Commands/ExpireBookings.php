<?php
namespace App\Console\Commands;

use App\Events\BookingExpired;
use App\Events\BookingStatusChanged;
use App\Models\Booking;
use Illuminate\Console\Command;

class ExpireBookings extends Command
{
    protected $signature   = 'booking:expire';
    protected $description = 'Update booking status to Selesai if time has passed';

    public function handle()
    {
        $expired = Booking::where('status', 'Diterima')
            ->where(function ($query) {
                $query->where('tanggal', '<', now()->format('Y-m-d'))
                    ->orWhere(function ($q) {
                        $q->where('tanggal', now()->format('Y-m-d'))
                            ->where('waktu_selesai', '<', now()->format('H:i:s'));
                    });
            })
            ->get();

        foreach ($expired as $booking) {
            $oldStatus = $booking->status;

            $booking->status = 'Selesai';
            $booking->save();

            // Trigger email + broadcast
            event(new BookingStatusChanged($booking, $oldStatus));
            event(new BookingExpired($booking));
        }

        $this->info("{$expired->count()} booking(s) marked as Selesai.");
    }
}
