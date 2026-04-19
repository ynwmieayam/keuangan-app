<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\LaporanHarianController;
use App\Http\Controllers\LaporanBulananController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root Route - Auto Redirect
|--------------------------------------------------------------------------
*/

// Route root - cek login atau belum
Route::get('/', function () {
    if (session()->has('admin')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Route untuk login (tidak perlu auth)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Route untuk logout (perlu auth)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('admin.auth');

/*
|--------------------------------------------------------------------------
| Protected Routes (Perlu Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['admin.auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Barang
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Pengeluaran
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::get('/pengeluaran/{id}', [PengeluaranController::class, 'show'])->name('pengeluaran.show');
    Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');

    // Pemasukan
    Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
    Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
    Route::put('/pemasukan/{id}', [PemasukanController::class, 'update'])->name('pemasukan.update');

    // Laporan Harian
    Route::get('/laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan.harian');

    // Laporan Bulanan
    Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporan.bulanan');
    
});