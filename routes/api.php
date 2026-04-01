<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\DendaApiController;
use App\Http\Controllers\Api\PeminjamanApiController;
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

    // ── Peminjaman Barang ────────────────────────────────────
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

    // ── Denda ────────────────────────────────────────────────
    Route::prefix('denda')->group(function () {
        Route::get('/', [DendaApiController::class, 'index']);
        Route::get('/{id}', [DendaApiController::class, 'show']);
        Route::post('/{id}/upload-bukti', [DendaApiController::class, 'uploadBukti']);
    });

});
