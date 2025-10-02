<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\JadwalController;
use App\Http\Controllers\Backend\RuanganController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index']);
Auth::routes();

Route::get('/booking', [FrontendController::class, 'booking']);
// Route::resource('/bookings', BookingController::class);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'backend.', 'middleware' => ['auth', Admin::class]], function () {
    Route::get('/', [BackendController::class, 'index'])->name('index');
    //crud
    Route::resource('/ruangan', RuanganController::class);
    Route::resource('/jadwal', JadwalController::class);
    Route::resource('/booking', BookingController::class);
    Route::resource('/user', UserController::class);
    Route::get('booking-export', [BookingController::class, 'export'])->name('booking.export');
});
    
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings/create', [UserBookingController::class, 'create'])->name('bookings.create');
    Route::post('/booking', [UserBookingController::class, 'store'])->name('bookings.store');
    Route::get('/booking/riwayat', [FrontendController::class, 'riwayat'])->name('bookings.riwayat');
    Route::get('bookings-export', [UserBookingController::class, 'export'])->name('bookings.export');
});

Route::get('/ruangan', [FrontendController::class, 'ruanganIndex'])->name('ruangan');
Route::get('/ruangan/{id}', [FrontendController::class, 'ruanganShow'])->name('ruangan.detail');

Route::post('/notifications/read', function () {
    \App\Models\booking::where('user_id', auth::id())
        ->whereIn('status', ['Diterima', 'Ditolak'])
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['success' => true]);
})->middleware('auth');
