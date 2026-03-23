<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PengurusManagementController;
use App\Http\Controllers\Pengurus\AnggotaManagementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
Route::post('/pengurus/register', [AuthController::class, 'registerPengurus']);
Route::post('/pengurus/login', [AuthController::class, 'loginPengurus']);
Route::post('/anggota/register', [AuthController::class, 'registerAnggota']);
Route::post('/anggota/login', [AuthController::class, 'loginAnggota']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'is_pengurus'])->group(function () {
    
    Route::get('/pengurus/akun-anggota', [AnggotaManagementController::class, 'index']);
    Route::get('/pengurus/detail-akun-anggota/{id}', [AnggotaManagementController::class, 'show']);
    Route::put('/pengurus/update-status-anggota/{id}', [AnggotaManagementController::class, 'updateStatus']);
    Route::delete('/pengurus/hapus-anggota/{id}', [AnggotaManagementController::class, 'destroy']);
    
});

Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/admin/akun-pengurus', [PengurusManagementController::class, 'index']);
    Route::get('/admin/detail-akun-pengurus/{id}', [PengurusManagementController::class, 'show']);
    Route::put('/admin/update-status-pengurus/{id}', [PengurusManagementController::class, 'updateStatus']);
    Route::delete('/admin/hapus-pengurus/{id}', [PengurusManagementController::class, 'destroy']);

});