<?php
namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\booking;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        setlocale(LC_TIME, 'id_ID.UTF-8');
        Carbon::setLocale('id');

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $notifications = booking::where('user_id', Auth::id())
                    ->whereIn('status', ['Diterima', 'Ditolak'])
                    ->where('is_read', false)
                    ->latest()
                    ->take(5)
                    ->get();

                $view->with('userNotifications', $notifications);
            }
        });
    }
}
