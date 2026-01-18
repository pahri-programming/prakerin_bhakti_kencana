<?php

use App\Http\Controllers\Api\BookingCheckController;
use App\Http\Controllers\Api\PeminjamanCheckController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\Backend\BarangController;
use App\Http\Controllers\Backend\BarangRuanganController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\JadwalController;
use App\Http\Controllers\Backend\KategoriController;
use App\Http\Controllers\Backend\LaporanUbkController;
use App\Http\Controllers\Backend\PeminjamanBarangController;
use App\Http\Controllers\Backend\PengembalianBarangController;
use App\Http\Controllers\Backend\RuanganController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserPeminjamanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| Public Routes (Guest & Authenticated)
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontendController::class, 'index'])->name('frontend.welcome');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Ruangan Routes (Public)
Route::get('/ruangan', [FrontendController::class, 'ruanganIndex'])->name('ruangan');
Route::get('/ruangan/{id}', [FrontendController::class, 'ruanganShow'])->name('ruangan.detail');

// Barang Routes (Public)
Route::get('/barang', [FrontendController::class, 'barangIndex'])->name('barang');
Route::get('/barang/{id}', [FrontendController::class, 'barangShow'])->name('barang.show');

// Booking Page (Public)
Route::get('/booking', [FrontendController::class, 'booking'])->name('frontend.booking');

/*
|--------------------------------------------------------------------------
| API Routes (Check Expired)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/peminjaman/check-expired', [PeminjamanCheckController::class, 'check'])
        ->name('api.peminjaman.check');
});

Route::get('/booking/check-expired', [BookingCheckController::class, 'check'])
    ->name('api.booking.check');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Notification
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // User Booking
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/create', [UserBookingController::class, 'create'])->name('create');
        Route::post('/', [UserBookingController::class, 'store'])->name('store');
    });

    // User Peminjaman
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/create', [UserPeminjamanController::class, 'create'])->name('create');
        Route::post('/', [UserPeminjamanController::class, 'store'])->name('store');
    });

                                                                                     // Riwayat (History)
    Route::get('/riwayat', [FrontendController::class, 'riwayat'])->name('riwayat'); // Alias
    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [FrontendController::class, 'riwayat'])->name('index');
        Route::get('/booking/export', [FrontendController::class, 'exportRiwayatBooking'])
            ->name('booking.export');
        Route::get('/peminjaman/export', [FrontendController::class, 'exportRiwayatPeminjaman'])
            ->name('peminjaman.export');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('backend.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [BackendController::class, 'index'])->name('index');

    /*
    |--------------------------------------------------------------------------
    | Master Data Management
    |--------------------------------------------------------------------------
    */

    // Kategori Management
    Route::resource('kategori', KategoriController::class);

    // Barang Management
    Route::prefix('barang')->name('barang.')->group(function () {
        Route::get('/export-pdf', [BarangController::class, 'export'])->name('exportpdf');
    });
    Route::resource('barang', BarangController::class);

    // Ruangan Management
    Route::prefix('ruangan')->name('ruangan.')->group(function () {
        Route::put('/{id}/status', [RuanganController::class, 'updateStatus'])->name('update-status');
    });
    Route::resource('ruangan', RuanganController::class);

    // Barang Ruangan Management
    Route::prefix('barangruangan')->name('barangruangan.')->group(function () {
        Route::put('/update-status/{id}', [BarangRuanganController::class, 'updateStatus'])
            ->name('update-status');
    });
    Route::resource('barangruangan', BarangRuanganController::class);

    // Jadwal Management
    Route::resource('jadwal', JadwalController::class);

    // User Management
    Route::resource('user', UserController::class);

    /*
    |--------------------------------------------------------------------------
    | Transaction Management
    |--------------------------------------------------------------------------
    */

    // Booking Management
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/export', [BookingController::class, 'export'])->name('export');
    });
    Route::resource('booking', BookingController::class);

    // Peminjaman Barang Management
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/export-pdf', [PeminjamanBarangController::class, 'export'])->name('pinjampdf');
    });
    Route::resource('peminjaman', PeminjamanBarangController::class);

    // Pengembalian Barang Management
    Route::prefix('pengembalian')->name('pengembalian.')->group(function () {
        Route::get('/export/pdf', [PengembalianBarangController::class, 'export'])->name('export');
        Route::put('/{id}/status', [PengembalianBarangController::class, 'updateStatus'])->name('update-status');
    });
    Route::resource('pengembalian', PengembalianBarangController::class);

    /*
    |--------------------------------------------------------------------------
    | Reports (Laporan)
    |--------------------------------------------------------------------------
    */

    Route::prefix('laporan-ubk')->name('laporan-ubk.')->group(function () {
        // Laporan Index
        Route::get('/', [LaporanUbkController::class, 'index'])->name('index');

        // Laporan Booking PDF
        Route::get('/booking/pdf', [LaporanUbkController::class, 'pdfBooking'])->name('pdf_booking');

        // Laporan Peminjaman PDF
        Route::get('/peminjaman/pdf', [LaporanUbkController::class, 'pdfPeminjaman'])->name('pdf_peminjaman');

        // Laporan Pengembalian PDF (NEW)
        Route::get('/pengembalian/pdf', [LaporanUbkController::class, 'pdfPengembalian'])->name('pdf_pengembalian');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route (Optional)
|--------------------------------------------------------------------------
| Uncomment if you want to handle 404 with custom page
*/
// Route::fallback(function () {
//     return view('errors.404');
// });
