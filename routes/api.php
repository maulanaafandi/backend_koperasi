<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\PengurusManagementController;
use App\Http\Controllers\Admin\NasabahController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\JenisSimpananController;
use App\Http\Controllers\Admin\LamaAngsuranController;
use App\Http\Controllers\Admin\PinjamanController;
use App\Http\Controllers\Pengurus\DashboardPengurusController;
use App\Http\Controllers\Pengurus\NasabahManagementController;
use App\Http\Controllers\Pengurus\TransaksiSimpananController;
use App\Http\Controllers\Pengurus\TransaksiTarikController;
use App\Http\Controllers\Pengurus\TransaksiPinjamanController;
use App\Http\Controllers\Pengurus\TransaksiBayarPinjamanController;
use App\Http\Controllers\Pengurus\PengumumanController;
use App\Http\Controllers\Nasabah\DashboardNasabahController;
use App\Http\Controllers\Nasabah\AktivitasTransaksiController;
use App\Http\Controllers\Nasabah\BayarPinjamanController;
use App\Http\Controllers\Nasabah\InboxController;
use App\Http\Controllers\Nasabah\JenisSimpananNasabahController;
use App\Http\Controllers\Nasabah\ProfileSettingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('check.origin')->group(function () {

});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/nasabah/login', [AuthController::class, 'login']);
Route::post('/daftar-ulang-pengurus', [AuthController::class, 'daftarUlangPengurus']);
Route::post('/daftar-ulang-nasabah', [AuthController::class, 'daftarUlangNasabah']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardAdminController::class, 'admin']);
    Route::get('/admin/nomor', [DashboardAdminController::class, 'getNamaAdmin']);
    Route::get('/admin/pengurus', [PengurusManagementController::class, 'index']);
    Route::get('/admin/get-nasabah', [NasabahController::class, 'indexNasabah']);
    Route::get('/admin/detail-nasabah/{id}', [NasabahController::class, 'detailNasabah']);
    Route::get('/admin/detail-akun-pengurus/{id}', [PengurusManagementController::class, 'show']);
    Route::post('/admin/buat-nomor-pengurus', [PengurusManagementController::class, 'store']);
    Route::patch('admin/update-status-pengurus/{id}', [PengurusManagementController::class, 'updateStatus']);
    Route::patch('admin/reset-password-pengurus/{id}', [PengurusManagementController::class, 'resetPassword']);

    Route::get('/admin/jenis-simpanan', [JenisSimpananController::class, 'index']);
    Route::post('/admin/buat-jenis-simpanan', [JenisSimpananController::class, 'store']);
    Route::get('/admin/detail-jenis-simpanan/{id}', [JenisSimpananController::class, 'show']);
    Route::get('/admin/get-jenis-simpanan/{id}', [JenisSimpananController::class, 'getById']);
    Route::patch('/admin/update-jenis-simpanan/{id}', [JenisSimpananController::class, 'update']);
    Route::delete('/admin/hapus-jenis-simpanan/{id}', [JenisSimpananController::class, 'destroy']);

    Route::get('/admin/lama-angsuran', [LamaAngsuranController::class, 'index']);
    Route::post('/admin/buat-lama-angsuran', [LamaAngsuranController::class, 'store']);
    Route::get('/admin/detail-lama-angsuran/{id}', [LamaAngsuranController::class, 'show']);
    Route::get('/admin/get-lama-angsuran/{id}', [LamaAngsuranController::class, 'getById']);
    Route::patch('/admin/update-lama-angsuran/{id}', [LamaAngsuranController::class, 'update']);
    Route::delete('/admin/hapus-lama-angsuran/{id}', [LamaAngsuranController::class, 'destroy']);

    Route::get('/admin/nasabah', [NasabahController::class, 'indexNasabah']);
    Route::get('/admin/detail-nasabah/{id}', [NasabahController::class, 'detailNasabah']);

    Route::get('/admin/transaksi', [TransaksiController::class, 'getTransaksi']);
    Route::get('/admin/detail-transaksi/{id}', [TransaksiController::class, 'detailTransaksi']);

    Route::get('/admin/pinjaman-pengajuan', [PinjamanController::class, 'pengajuan']);
    Route::get('/admin/detail-pinjaman-pengajuan/{id}', [PinjamanController::class, 'detailPengajuan']);
    Route::get('/admin/pinjaman-jatuh-tempo', [PinjamanController::class, 'jatuhTempo']);
    Route::get('/admin/detail-pinjaman-jatuh-tempo/{id}', [PinjamanController::class, 'detailJatuhTempo']);
    Route::get('/admin/pinjaman-macet', [PinjamanController::class, 'macet']);
    Route::get('/admin/detail-pinjaman-macet/{id}', [PinjamanController::class, 'detailMacet']);
    Route::get('/admin/pinjaman-lunas', [PinjamanController::class, 'lunas']);
    Route::get('/admin/detail-pinjaman-lunas/{id}', [PinjamanController::class, 'detailLunas']);

});

Route::middleware(['auth:sanctum', 'is_pengurus'])->group(function () {
    Route::get('/pengurus/dashboard', [DashboardPengurusController::class, 'index']);
    Route::get('/pengurus/nama', [DashboardPengurusController::class, 'getNamaPengurus']);
    Route::get('/pengurus/nasabah', [NasabahManagementController::class, 'index']);
    Route::get('/pengurus/jenis-simpanan-nasabah', [NasabahManagementController::class, 'getJenisSimpanan']);
    Route::get('/pengurus/detail-akun-nasabah/{id}', [NasabahManagementController::class, 'show']);
    Route::get('/pengurus/get-status-akun-nasabah/{id}', [NasabahManagementController::class, 'getStatus']);
    Route::post('/pengurus/buat-nasabah', [NasabahManagementController::class, 'store']);
    Route::patch('/pengurus/update-status-akun-nasabah/{id}', [NasabahManagementController::class, 'updateStatus']);

    Route::post('/pengurus/buat-transaksi-simpanan', [TransaksiSimpananController::class, 'simpan']);
    Route::get('/pengurus/transaksi-simpanan', [TransaksiSimpananController::class, 'indexSimpan']);
    Route::get('/pengurus/detail-transaksi-simpanan/{id}', [TransaksiSimpananController::class, 'detailSimpan']);

    Route::post('/pengurus/buat-transaksi-tarik', [TransaksiTarikController::class, 'tarik']);
    Route::get('/pengurus/transaksi-tarik', [TransaksiTarikController::class, 'indexTarik']);
    Route::get('/pengurus/detail-transaksi-tarik/{id}', [TransaksiTarikController::class, 'detailTarik']);

    Route::get('/pengurus/transaksi-pinjam', [TransaksiPinjamanController::class, 'index']);
    Route::get('/pengurus/detail-transaksi-pinjam/{id}', [TransaksiPinjamanController::class, 'show']);
    Route::get('/pengurus/lama-angsuran-nasabah', [TransaksiPinjamanController::class, 'getAllTenor']);
    Route::get('/pengurus/status-transaksi-pinjam/{id}', [TransaksiPinjamanController::class, 'getStatus']);
    Route::post('/pengurus/buat-transaksi-pinjam', [TransaksiPinjamanController::class, 'store']);
    Route::patch('/pengurus/update-transaksi-pinjam/{id}', [TransaksiPinjamanController::class, 'approve']);

    Route::get('/pengurus/transaksi-bayar-pinjaman', [TransaksiBayarPinjamanController::class, 'getBayarPinjaman']);
    Route::get('/pengurus/detail-transaksi-bayar-pinjaman/{id}', [TransaksiBayarPinjamanController::class, 'detailBayarPinjaman']);
    Route::post('/pengurus/buat-transaksi-bayar-pinjaman', [TransaksiBayarPinjamanController::class, 'bayarCicilan']);

    Route::get('/pengurus/pengumuman', [PengumumanController::class, 'index']);
    Route::get('/pengurus/get-pengumuman/{id}', [PengumumanController::class, 'show']);
    Route::get('/pengurus/detail-pengumuman/{id}', [PengumumanController::class, 'detailpengumuman']);
    Route::post('/pengurus/buat-pengumuman', [PengumumanController::class, 'store']);
    Route::patch('/pengurus/update-pengumuman/{id}', [PengumumanController::class, 'update']);
    Route::delete('/pengurus/hapus-pengumuman/{id}', [PengumumanController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'is_nasabah'])->group(function () {
    Route::get('/nasabah/dashboard', [DashboardNasabahController::class, 'dashboardNasabah']);

    Route::get('/nasabah/aktivitas-transaksi', [AktivitasTransaksiController::class, 'aktivitasTransaksiNasabah']);
    Route::get('/nasabah/detail-aktivitas-transaksi/{id}', [AktivitasTransaksiController::class, 'detailAktivitasTransaksi']);

    Route::post('/nasabah/bayar-cicilan-pinjaman/{id}', [BayarPinjamanController::class, ' bayarCicilanNasabah']);
    Route::get('/nasabah/bayar-pinjaman', [BayarPinjamanController::class, 'getPinjamanNasabah']);
    Route::get('/nasabah/detail-bayar-pinjaman/{id}', [BayarPinjamanController::class, 'getDetailPinjamanNasabah']);

    Route::get('/nasabah/inbox', [InboxController::class, 'getInbox']);
    Route::get('/nasabah/inbox/{id}', [InboxController::class, 'getDetailInbox']);

    Route::get('/nasabah/jenis-simpanan', [JenisSimpananNasabahController::class, 'getJenisSimpanan']);

    Route::get('/nasabah/profil-akun', [ProfileSettingController::class, 'profileNasabah']);
    Route::get('/nasabah/get-profil-akun', [ProfileSettingController::class, 'getNasabahById']);
    Route::patch('/nasabah/update-profil', [ProfileSettingController::class, 'updateProfileNasabah']);
    Route::patch('/nasabah/update-pin', [ProfileSettingController::class, 'updatePinNasabah']);
    Route::patch('/nasabah/update-password', [ProfileSettingController::class, 'updatePasswordNasabah']);
});

