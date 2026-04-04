<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PengurusManagementController;
use App\Http\Controllers\Admin\JenisSimpananController;
use App\Http\Controllers\Admin\LamaAngsuranController;
use App\Http\Controllers\Pengurus\AnggotaManagementController;
use App\Http\Controllers\Pengurus\PinjamanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::post('/pengurus/login', [AuthController::class, 'loginPengurus']);
Route::post('/daftar-ulang-pengurus', [AuthController::class, 'daftarUlangPengurus']);
Route::post('/anggota/register', [AuthController::class, 'registerAnggota']);
Route::post('/anggota/login', [AuthController::class, 'loginAnggota']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/admin/pengurus', [PengurusManagementController::class, 'index']);
    Route::get('/admin/detail-akun-pengurus/{id}', [PengurusManagementController::class, 'show']);
    Route::post('/admin/buat-nomor-pengurus', [PengurusManagementController::class, 'store']);
    Route::patch('admin/update-status-pengurus/{id}', [PengurusManagementController::class, 'updateStatus']);
    Route::patch('/admin/reset-password-pengurus/{id}', [PengurusManagementController::class, 'resetPassword']);

    Route::get('/admin/jenis-simpanan', [JenisSimpananController::class, 'index']);
    Route::post('/admin/buat-jenis-simpanan', [JenisSimpananController::class, 'store']);
    Route::get('/admin/jenis-simpanan/{id}', [JenisSimpananController::class, 'show']);
    Route::patch('/admin/update-jenis-simpanan/{id}', [JenisSimpananController::class, 'update']);

    Route::get('/admin/lama-angsuran', [LamaAngsuranController::class, 'index']);
    Route::post('/admin/buat-lama-angsuran', [LamaAngsuranController::class, 'store']);
    Route::get('/admin/detail-lama-angsuran/{id}', [LamaAngsuranController::class, 'show']);
    Route::patch('/admin/update-lama-angsuran/{id}', [LamaAngsuranController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'is_pengurus'])->group(function () {
    
    // Route::get('/pengurus/akun-anggota', [AnggotaManagementController::class, 'index']);
    // Route::get('/pengurus/detail-akun-anggota/{id}', [AnggotaManagementController::class, 'show']);
    // Route::put('/pengurus/update-status-anggota/{id}', [AnggotaManagementController::class, 'updateStatus']);
    // Route::delete('/pengurus/hapus-anggota/{id}', [AnggotaManagementController::class, 'destroy']);

    // Route::get('/pengurus/pinjaman', [PinjamanController::class, 'index']);
    // Route::post('/pengurus/pinjaman', [PinjamanController::class, 'store']);
    // Route::put('/pengurus/pinjaman/approve/{id}', [PinjamanController::class, 'approve']);
    // Route::delete('/pengurus/pinjaman/{id}', [PinjamanController::class, 'destroy']);
    
});

