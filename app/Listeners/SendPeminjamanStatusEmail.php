<?php

namespace App\Listeners;

use App\Events\PeminjamanStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPeminjamanStatusEmail
{
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
    public function handle(PeminjamanStatusChanged $event): void
    {
        //
    }
}
