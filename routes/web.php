<?php

use App\Http\Controllers\Api\BookingCheckController;
use App\Http\Controllers\Api\PeminjamanCheckController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\Backend\BarangController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\JadwalController;
use App\Http\Controllers\Backend\KategoriController;
use App\Http\Controllers\Backend\LaporanUbkController;
use App\Http\Controllers\Backend\PeminjamanBarangController;
use App\Http\Controllers\Backend\RuanganController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\BookingNotificationController;
use App\Http\Controllers\User\PeminjamanNotificationController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserPeminjamanController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index'])->name('frontend.welcome');
Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::middleware('auth')
    ->get('/peminjaman/check-expired', [PeminjamanCheckController::class, 'check'])
    ->name('api.peminjaman.check');
Route::get('/booking/check-expired', [BookingCheckController::class, 'check'])
    ->name('api.booking.check');

Auth::routes();

Route::get('/booking', [FrontendController::class, 'booking']);
// Route::resource('/bookings', BookingController::class);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'as' => 'backend.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [BackendController::class, 'index'])->name('index');
    //crud
    Route::resource('/ruangan', RuanganController::class);
    Route::resource('/jadwal', JadwalController::class);
    Route::resource('/booking', BookingController::class);
    Route::get('/peminjaman-barang/export-pdf', [PeminjamanBarangController::class, 'export'])->name('peminjaman.pinjampdf');
    Route::resource('/peminjaman', PeminjamanBarangController::class);
    Route::resource('/kategori', KategoriController::class);
    Route::get('barang/export-pdf', [BarangController::class, 'export'])->name('barang.exportpdf');
    Route::resource('/barang', BarangController::class);
    Route::resource('/user', UserController::class);
    Route::get('booking-export', [BookingController::class, 'export'])->name('booking.export');
    Route::get('/laporan-ubk', [LaporanUbkController::class, 'index'])
        ->name('laporan-ubk.index');
    // Laporan Booking UBK
    Route::get('/laporan-ubk/booking/pdf', [LaporanUbkController::class, 'pdfBooking'])
        ->name('laporan-ubk.pdf_booking');
    // Laporan Peminjaman UBK
    Route::get('/laporan-ubk/peminjaman/pdf', [LaporanUbkController::class, 'pdfPeminjaman'])
        ->name('laporan-ubk.pdf_peminjaman');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/bookings/create', [UserBookingController::class, 'create'])->name('bookings.create');
    Route::post('/booking', [UserBookingController::class, 'store'])->name('bookings.store');
    Route::get('/peminjaman/create', [UserPeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [UserPeminjamanController::class, 'store'])->name('peminjaman.store');

    Route::get('/riwayat', [FrontendController::class, 'riwayat'])->name('riwayat');

    Route::get('/riwayat/booking/export', [FrontendController::class, 'exportRiwayatBooking'])
        ->name('riwayat.booking.export');

    Route::get('/riwayat/peminjaman/export', [FrontendController::class, 'exportRiwayatPeminjaman'])
        ->name('riwayat.peminjaman.export');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

});

Route::get('/ruangan', [FrontendController::class, 'ruanganIndex'])->name('ruangan');
Route::get('/ruangan/{id}', [FrontendController::class, 'ruanganShow'])->name('ruangan.detail');
Route::get('/barang', [FrontendController::class, 'barangIndex'])
    ->name('barang');
Route::get('/barang/{id}', [FrontendController::class, 'barangShow'])->name('barang.show');

// Booking
Route::post('/booking-notifications/read', [BookingNotificationController::class, 'markAsRead'])
    ->name('booking.notifications.read');

// Peminjaman
Route::post('/peminjaman-notifications/read', [PeminjamanNotificationController::class, 'markAsRead'])
    ->name('peminjaman.notifications.read');

// Route::post('/notifications/read-all', function () {
//     $userId = Auth::id();

//     // Tandai booking
//     App\Models\Booking::where('user_id', $userId)
//         ->whereIn('status', ['Diterima', 'Ditolak'])
//         ->where('is_read', false)
//         ->update(['is_read' => true]);

//     // Tandai peminjaman
//     App\Models\PeminjamanBarang::where('user_id', $userId)
//         ->whereIn('status', ['disetujui', 'ditolak', 'selesai'])
//         ->where('is_read', false)
//         ->update(['is_read' => true]);

//     return response()->json(['success' => true]);
// })->middleware('auth');
