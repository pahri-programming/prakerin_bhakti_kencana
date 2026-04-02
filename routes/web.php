<?php

use App\Http\Controllers\Api\BookingCheckController;
use App\Http\Controllers\Api\PeminjamanCheckController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\Backend\BarangController;
use App\Http\Controllers\Backend\BarangRuanganController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\DendaBookingController;
use App\Http\Controllers\Backend\DendaPengembalianController;
use App\Http\Controllers\Backend\JadwalController;
use App\Http\Controllers\Backend\KategoriController;
use App\Http\Controllers\Backend\LaporanUbkController;
use App\Http\Controllers\Backend\PeminjamanBarangController;
use App\Http\Controllers\Backend\PengembalianBarangController;
use App\Http\Controllers\Backend\RuanganController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Pic\PicDashboardController;
use App\Http\Controllers\Pic\VerifikasiBookingController;
use App\Http\Controllers\Pic\VerifikasiPeminjamanController;
use App\Http\Controllers\Pic\VerifikasiPengembalianController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserBookingController;
use App\Http\Controllers\User\UserDendaBookingController;
use App\Http\Controllers\User\UserDendaController;
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

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // User Booking
    Route::prefix('bookings')->name('user.booking.')->group(function () {
        Route::get('/', [UserBookingController::class, 'index'])->name('index');
        Route::get('/create', [UserBookingController::class, 'create'])->name('create');
        Route::post('/', [UserBookingController::class, 'store'])->name('store');
        Route::get('/{id}', [UserBookingController::class, 'show'])->name('show');
        Route::delete('/{id}', [UserBookingController::class, 'destroy'])->name('destroy');
    });

    // User Peminjaman
    Route::prefix('peminjaman')->name('user.peminjaman.')->group(function () {
        Route::get('/', [UserPeminjamanController::class, 'index'])->name('index');
        Route::get('/create', [UserPeminjamanController::class, 'create'])->name('create');
        Route::post('/', [UserPeminjamanController::class, 'store'])->name('store');
        Route::get('/{id}', [UserPeminjamanController::class, 'show'])->name('show');
        Route::delete('/{id}', [UserPeminjamanController::class, 'destroy'])->name('destroy');
    });

    // User Denda

    // Group USER - hanya untuk user
    Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
        Route::get('/denda', [UserDendaController::class, 'index'])->name('denda.index');
        Route::get('/denda/{id}', [UserDendaController::class, 'show'])->name('denda.show');
        Route::post('/denda/{id}/upload-bukti', [UserDendaController::class, 'uploadBukti'])->name('denda.upload-bukti');
    });

    Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
        Route::get('/denda-booking', [UserDendaBookingController::class, 'index'])->name('denda-booking.index');
        Route::get('/denda-booking/{id}', [UserDendaBookingController::class, 'show'])->name('denda-booking.show');
        Route::post('/denda-booking/{id}/upload-bukti', [UserDendaBookingController::class, 'uploadBukti'])->name('denda-booking.upload-bukti');
    });

    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatController::class, 'index'])->name('index');
        Route::get('/booking/export', [RiwayatController::class, 'exportBooking'])->name('booking.export');
        Route::get('/peminjaman/export', [RiwayatController::class, 'exportPeminjaman'])->name('peminjaman.export');
    });

});

/*
|--------------------------------------------------------------------------
|  PIC Routes (Person In Charge - Petugas Pengecekan)
|--------------------------------------------------------------------------
*/
Route::prefix('pic')->name('pic.')->middleware(['auth', 'pic:pic'])->group(function () {

    // Dashboard PIC
    Route::get('/', [PicDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [PicDashboardController::class, 'index'])->name('dashboard.index');

    //
    // Verifikasi Peminjaman Barang
    //
    Route::prefix('verifikasi-peminjaman')->name('verifikasi-peminjaman.')->group(function () {
        Route::get('/', [VerifikasiPeminjamanController::class, 'index'])->name('index');
        Route::get('/create/{id}', [VerifikasiPeminjamanController::class, 'create'])->name('create');
        Route::get('/show/{id}', [VerifikasiPeminjamanController::class, 'show'])->name('show');
        Route::post('/store/{id}', [VerifikasiPeminjamanController::class, 'store'])->name('store');
    });

    //
    // Verifikasi Booking Ruangan
    //
    Route::prefix('verifikasi-booking')->name('verifikasi-booking.')->group(function () {
        Route::get('/', [VerifikasiBookingController::class, 'index'])->name('index');
        Route::get('/create/{id}', [VerifikasiBookingController::class, 'create'])->name('create');
        Route::get('/show/{id}', [VerifikasiBookingController::class, 'show'])->name('show');
        Route::post('/store/{id}', [VerifikasiBookingController::class, 'store'])->name('store');
    });

    // Verifikasi Pengembalian Barang (NEW!)
    Route::prefix('verifikasi-pengembalian')->name('verifikasi-pengembalian.')->group(function () {
        Route::get('/', [VerifikasiPengembalianController::class, 'index'])->name('index');
        Route::get('/create/{id}', [VerifikasiPengembalianController::class, 'create'])->name('create');
        Route::get('/show/{id}', [VerifikasiPengembalianController::class, 'show'])->name('show');
        Route::post('/store/{id}', [VerifikasiPengembalianController::class, 'store'])->name('store');
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

    Route::patch('booking/{id}/approve', [BookingController::class, 'approve'])
        ->name('booking.approve');

    Route::patch('booking/{id}/reject', [BookingController::class, 'reject'])
        ->name('booking.reject');

    Route::patch('booking/{id}/complete', [BookingController::class, 'complete'])
        ->name('booking.complete');

    // Booking Export
    Route::get('booking/export', [BookingController::class, 'export'])
        ->name('booking.export');

    Route::resource('booking', BookingController::class);

    // Peminjaman Barang Management
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/export-pdf', [PeminjamanBarangController::class, 'export'])->name('pinjampdf');
    });
    Route::resource('peminjaman', PeminjamanBarangController::class);
    // Peminjaman Barang Management
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/export-pdf', [PeminjamanBarangController::class, 'export'])->name('pinjampdf');
        Route::patch('/{id}/update-status', [PeminjamanBarangController::class, 'updateStatus'])->name('updateStatus'); // tambah ini
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
    | Laporan Verifikasi dari PIC (Admin View)
    |--------------------------------------------------------------------------
    */
    Route::prefix('denda')->name('denda.')->group(function () {
        // List pending verifikasi (perlu tindak lanjut)
        Route::get('/', [App\Http\Controllers\Backend\DendaPengembalianController::class, 'index'])
            ->name('index');

        // Form tindak lanjut & input denda
        Route::get('/tindak-lanjut/{id}', [App\Http\Controllers\Backend\DendaPengembalianController::class, 'tindakLanjut'])
            ->name('tindak-lanjut');

        // Store denda
        Route::post('/store/{id}', [App\Http\Controllers\Backend\DendaPengembalianController::class, 'store'])
            ->name('store');

        // List semua denda
        Route::get('/list', [App\Http\Controllers\Backend\DendaPengembalianController::class, 'listDenda'])
            ->name('list');

        // Verifikasi pembayaran denda
        Route::post('/verifikasi-bayar/{id}', [App\Http\Controllers\Backend\DendaPengembalianController::class, 'verifikasiBayar'])
            ->name('verifikasi-bayar');
    });

    // Approve/Reject bukti denda pengembalian
    Route::post('denda/{id}/approve-bukti', [DendaPengembalianController::class, 'approveBukti'])
        ->name('denda.approve-bukti');
    Route::post('denda/{id}/reject-bukti', [DendaPengembalianController::class, 'rejectBukti'])
        ->name('denda.reject-bukti');

    // Denda Booking
    Route::prefix('denda-booking')->name('denda-booking.')->group(function () {
        Route::get('/', [DendaBookingController::class, 'index'])->name('index');
        Route::get('/list', [DendaBookingController::class, 'listDenda'])->name('list');
        Route::get('/{id}/tindak-lanjut', [DendaBookingController::class, 'tindakLanjut'])->name('tindak-lanjut');
        Route::post('/{id}/store', [DendaBookingController::class, 'store'])->name('store');
        Route::post('/{id}/approve-bukti', [DendaBookingController::class, 'approveBukti'])->name('approve-bukti');
        Route::post('/{id}/reject-bukti', [DendaBookingController::class, 'rejectBukti'])->name('reject-bukti');
    });

    Route::prefix('verifikasi/laporan')->name('verifikasi.laporan.')->group(function () {

        //  LAPORAN PEMINJAMAN
        Route::get('/peminjaman', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'laporanPeminjaman'])
            ->name('peminjaman');

        Route::get('/peminjaman/{id}/detail-barang', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'getDetailBarang'])
            ->name('peminjaman.detail-barang');

        Route::get('/peminjaman/{id}/detail', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'detailPeminjaman'])
            ->name('peminjaman.detail');

        Route::put('/peminjaman/{id}/tindakan', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'tindakanPeminjaman'])
            ->name('peminjaman.tindakan');

        Route::get('/peminjaman/export-pdf', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'exportPeminjaman'])
            ->name('peminjaman.export');

        //  LAPORAN BOOKING
        Route::get('/booking', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'laporanBooking'])
            ->name('booking');

        Route::get('/booking/{id}/detail', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'detailBooking'])
            ->name('booking.detail');

        Route::put('/booking/{id}/tindakan', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'tindakanBooking'])
            ->name('booking.tindakan');

        Route::get('/booking/export-pdf', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'exportBooking'])
            ->name('booking.export');

        // LAPORAN PENGEMBALIAN (NEW!)
        Route::get('/pengembalian', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'laporanPengembalian'])
            ->name('pengembalian');

        Route::get('/pengembalian/{id}/detail', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'detailPengembalian'])
            ->name('pengembalian.detail');

        Route::put('/pengembalian/{id}/tindakan', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'tindakanPengembalian'])
            ->name('pengembalian.tindakan');

        Route::get('/pengembalian/export-pdf', [App\Http\Controllers\Backend\LaporanVerifikasiController::class, 'exportPengembalian'])
            ->name('pengembalian.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports (Laporan UBK)
    |--------------------------------------------------------------------------
    */
    Route::prefix('laporan-ubk')->name('laporan-ubk.')->group(function () {
        Route::get('/', [LaporanUbkController::class, 'index'])->name('index');
        Route::get('/booking/pdf', [LaporanUbkController::class, 'pdfBooking'])->name('pdf_booking');
        Route::get('/peminjaman/pdf', [LaporanUbkController::class, 'pdfPeminjaman'])->name('pdf_peminjaman');
        Route::get('/pengembalian/pdf', [LaporanUbkController::class, 'pdfPengembalian'])->name('pdf_pengembalian');
    });
});

/*
|--------------------------------------------------------------------------
| Google OAuth Routes
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
