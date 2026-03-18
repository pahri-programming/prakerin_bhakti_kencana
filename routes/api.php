<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RuanganController;

/*
|--------------------------------------------------------------------------
| API Routes 
|--------------------------------------------------------------------------
| 
| Here is where you can register API routes for your application. These
| are loaded by the RouteServiceProvider within a group which
| Base URL: http://localhost:8000/api
|
*/

// ========================================
// Auth Routes (Register & Login)
// ========================================

Route::prefix('auth')->group(function () {

    // Register - Daftar user baru
    // POST http://localhost:8000/api/auth/register
    Route::post('/register', [AuthController::class, 'register']);

    // Login - Masuk ke sistem
    // POST http://localhost:8000/api/auth/login
    Route::post('/login', [AuthController::class, 'login']);

});

// Get All Ruangan - Ambil semua data ruangan
// GET http://localhost:8000/api/ruangan
Route::get('/ruangan', [RuanganController::class, 'index']);

// Get Single Ruangan - Ambil 1 data ruangan
// GET http://localhost:8000/api/ruangan/{id}
Route::get('/ruangan/{id}', [RuanganController::class, 'show']);

// Create Ruangan - Buat data ruangan baru
// POST http://localhost:8000/api/ruangan
Route::post('/ruangan/create', [RuanganController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

    // Logout - Keluar dari sistem
    // POST http://localhost:8000/api/auth/logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);

});
