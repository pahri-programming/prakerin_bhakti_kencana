<?php
namespace App\Providers;

use App\Models\booking;
use App\Models\PeminjamanBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
            $userId = Auth::id();

            if ($userId) {
                $userNotifications = Booking::where('user_id', $userId)
                    ->whereIn('status', ['Diterima', 'Ditolak'])
                    ->where('is_read', false)
                    ->latest()
                    ->get();

                $userBorrowNotifications = PeminjamanBarang::where('user_id', $userId)
                    ->whereIn('status', ['disetujui', 'ditolak', 'selesai'])
                    ->where('is_read', false)
                    ->latest()
                    ->get();
            } else {
                $userNotifications       = collect();
                $userBorrowNotifications = collect();
            }

            $view->with(compact('userNotifications', 'userBorrowNotifications'));
        });
    }
}
