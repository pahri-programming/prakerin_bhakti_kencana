<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\DendaApiController;
use App\Http\Controllers\Api\PeminjamanApiController;
use App\Http\Controllers\Api\RiwayatApiController;
use App\Http\Controllers\Api\RuanganController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
| Base URL: http://localhost:8000/api
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIC ROUTES (tidak perlu login)
// ============================================================

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Barang & Ruangan (bisa dilihat tanpa login)
Route::get('/barang', [BarangApiController::class, 'index']);
Route::get('/barang/{id}', [BarangApiController::class, 'show']);
Route::get('/ruangan', [RuanganController::class, 'index']);
Route::get('/ruangan/{id}', [RuanganController::class, 'show']);

// ============================================================
// PROTECTED ROUTES (wajib login, pakai token Sanctum)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ─────────────────────────────────────────────────
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // ── Peminjaman Barang ────────────────────────────────────6
    Route::prefix('peminjaman')->group(function () {
        Route::get('/', [PeminjamanApiController::class, 'index']);
        Route::post('/', [PeminjamanApiController::class, 'store']);
        Route::get('/{id}', [PeminjamanApiController::class, 'show']);
        Route::delete('/{id}', [PeminjamanApiController::class, 'destroy']);
    });

    // ── Booking Ruangan ──────────────────────────────────────
    Route::prefix('booking')->group(function () {
        Route::get('/', [BookingApiController::class, 'index']);
        Route::post('/', [BookingApiController::class, 'store']);
        Route::get('/ruangan-tersedia', [BookingApiController::class, 'ruanganTersedia']);
        Route::get('/{id}', [BookingApiController::class, 'show']);
        Route::delete('/{id}', [BookingApiController::class, 'destroy']);

    });

    // Riwayat
    Route::get('/riwayat', [RiwayatApiController::class, 'index']);

    // Profile
    Route::get('/profile', [ProfileApiController::class, 'index']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);

    // ── Denda ────────────────────────────────────────────────
    Route::prefix('denda')->group(function () {
        Route::get('/', [DendaApiController::class, 'index']);                                // semua denda user
        Route::get('/{type}/{id}', [DendaApiController::class, 'show']);                      // detail (type: barang/booking)
        Route::post('/{type}/{id}/upload-bukti', [DendaApiController::class, 'uploadBukti']); // upload bukti pembayaran denda
    });

});
