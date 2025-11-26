<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Events\BookingExpired;
use Illuminate\Support\Facades\Log;

class BookingCheckController extends Controller
{
    public function check()
    {
        $now = now();
        Log::info('CHECK BOOKING EXPIRED', [
            'now' => $now->format('Y-m-d H:i:s'),
            'time' => $now->format('H:i:s')
        ]);

        $expired = Booking::whereNotIn('status', ['Selesai', 'Ditolak'])
            ->where(function ($q) use ($now) {
                $q->where('tanggal', '<', $now->toDateString())
                  ->orWhere(function ($s) use ($now) {
                      $s->where('tanggal', $now->toDateString())
                        ->where('waktu_selesai', '<', $now->format('H:i:s'));
                  });
            })
            ->get();

        Log::info('FOUND BOOKING EXPIRED', ['count' => $expired->count()]);

        $updated = 0;
        foreach ($expired as $b) {
            $b->update(['status' => 'Selesai']);
            event(new BookingExpired($b));
            $updated++;
        }

        return response()->json(['updated' => $updated]);
    }
}