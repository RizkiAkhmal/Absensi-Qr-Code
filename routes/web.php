<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PublicScanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Public Scanner Routes (No Authentication Required)
Route::get('/scanner', [PublicScanController::class, 'showScanner'])->name('public.scanner');
Route::post('/public/scan-absensi', [PublicScanController::class, 'processAttendance'])->name('public.scan.absensi');

// Public Scanner Routes (No Authentication Required)
Route::get('/scanner', [PublicScanController::class, 'showScanner'])->name('public.scanner');
Route::post('/public/scan-absensi', [PublicScanController::class, 'processAttendance'])->name('public.scan.absensi');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pegawai Management
    Route::get('/pegawai', [AdminController::class, 'pegawai'])->name('pegawai');
    Route::get('/pegawai/create', [AdminController::class, 'createPegawai'])->name('pegawai.create');
    Route::post('/pegawai', [AdminController::class, 'storePegawai'])->name('pegawai.store');
    Route::get('/pegawai/{user}/edit', [AdminController::class, 'editPegawai'])->name('pegawai.edit');
    Route::put('/pegawai/{user}', [AdminController::class, 'updatePegawai'])->name('pegawai.update');
    Route::delete('/pegawai/{user}', [AdminController::class, 'deletePegawai'])->name('pegawai.delete');

    // Jadwal Kerja Management
    Route::get('/jadwal', [AdminController::class, 'jadwalKerja'])->name('jadwal');
    Route::get('/jadwal/create/{userId}', [AdminController::class, 'createJadwal'])->name('jadwal.create');
    Route::post('/jadwal', [AdminController::class, 'storeJadwal'])->name('jadwal.store');
    Route::post('/jadwal/copy', [AdminController::class, 'copyJadwal'])->name('jadwal.copy');
    Route::get('/jadwal/bulk-create', [AdminController::class, 'bulkCreateJadwal'])->name('jadwal.bulk.create');
    Route::post('/jadwal/bulk-store', [AdminController::class, 'bulkStoreJadwal'])->name('jadwal.bulk.store');

    // Laporan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/filter', [AdminController::class, 'laporanFilter'])->name('laporan.filter');

    // QR Code Generation
    Route::get('/generate-qr', [AdminController::class, 'generateQR'])->name('generate-qr');
});

// Pegawai Routes
Route::middleware(['auth', 'role:pegawai'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('dashboard');
    Route::get('/qrcode', [PegawaiController::class, 'showQRCode'])->name('qrcode');
    Route::get('/absensi', [PegawaiController::class, 'absensi'])->name('absensi');
    Route::get('/jadwal', [PegawaiController::class, 'jadwal'])->name('jadwal');
});


