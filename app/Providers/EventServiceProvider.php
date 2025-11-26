<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        \App\Events\BookingStatusChanged::class => [
            \App\Listeners\SendBookingStatusEmail::class,
        ],
        // BookingExpired bisa pake schedule, bukan listener
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
