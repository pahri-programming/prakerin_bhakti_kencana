<?php
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\RuanganController;
use App\Http\Controllers\Backend\JadwalController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Middleware\Admin;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\User\UserBookingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'backend.', 'middleware' => ['auth', Admin::class]], function () {
    Route::get('/', [BackendController::class, 'index'])->name('index');
    Route::resource('/ruangan', RuanganController::class);
    Route::resource('/jadwal', JadwalController::class);
    Route::resource('/booking', BookingController::class);
    Route::resource('/user', UserController::class);
    Route::get('booking-export', [BookingController::class, 'export'])->name('booking.export');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/booking', [UserBookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/create', [UserBookingController::class, 'create'])->name('.booking.create');
    Route::post('/booking/riwayat', [UserBookingController::class, 'riwayat'])->name('booking.riwayat');
    Route::get('/ruangan', [UserBookingController::class, 'show'])->name('ruangan.show');
    Route::get('/ruangan/{id}', [UserBookingController::class, 'tampil'])->name('ruangan.show');
});


