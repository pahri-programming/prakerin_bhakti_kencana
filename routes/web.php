<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\Backend\BarangController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\JadwalController;
use App\Http\Controllers\Backend\KategoriController;
use App\Http\Controllers\Backend\PeminjamanBarangController;
use App\Http\Controllers\Backend\RuanganController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Middleware\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('frontend.welcome');
Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

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
    Route::resource('/peminjaman-barang', PeminjamanBarangController::class);
    Route::resource('/kategori', KategoriController::class);
    Route::resource('/barang', BarangController::class);
    Route::get('/barang/exportpdf', [BarangController::class, 'export'])->name('barang.exportpdf');
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

Route::post('/notifications/read-all', function () {
    $userId = Auth::id();

    // Tandai booking
    App\Models\Booking::where('user_id', $userId)
        ->whereIn('status', ['Diterima', 'Ditolak'])
        ->where('is_read', false)
        ->update(['is_read' => true]);

    // Tandai peminjaman
    App\Models\PeminjamanBarang::where('user_id', $userId)
        ->whereIn('status', ['disetujui', 'ditolak', 'selesai'])
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['success' => true]);
})->middleware('auth');
