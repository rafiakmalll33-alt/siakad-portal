<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\KrsApprovalController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboard;
use App\Http\Controllers\Dosen\KrsApprovalController as DosenKrsApproval;
use App\Http\Controllers\Dosen\AbsensiController;
use App\Http\Controllers\Dosen\NilaiController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboard;
use App\Http\Controllers\Mahasiswa\KrsController;
use App\Http\Controllers\Mahasiswa\PembayaranController as MahasiswaPembayaran;
use App\Http\Controllers\Mahasiswa\NilaiController as MahasiswaNilai;

// Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.store');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
    Route::resource('mahasiswa', MahasiswaController::class, ['as' => 'admin']);
    
    Route::prefix('krs')->group(function () {
        Route::get('/', [KrsApprovalController::class, 'index'])->name('admin.krs.index');
        Route::get('{krs}', [KrsApprovalController::class, 'detail'])->name('admin.krs.detail');
        Route::post('{krs}/approve', [KrsApprovalController::class, 'approve'])->name('admin.krs.approve');
        Route::post('{krs}/reject', [KrsApprovalController::class, 'reject'])->name('admin.krs.reject');
    });
    
    Route::prefix('pembayaran')->group(function () {
        Route::get('/', [PembayaranController::class, 'index'])->name('admin.pembayaran.index');
        Route::get('{pembayaran}', [PembayaranController::class, 'show'])->name('admin.pembayaran.show');
        Route::post('{pembayaran}/verify', [PembayaranController::class, 'verify'])->name('admin.pembayaran.verify');
        Route::get('create-cash', [PembayaranController::class, 'createCash'])->name('admin.pembayaran.create-cash');
        Route::post('store-cash', [PembayaranController::class, 'storeCash'])->name('admin.pembayaran.store-cash');
    });
});

// Dosen Routes
Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->group(function () {
    Route::get('dashboard', [DosenDashboard::class, 'index'])->name('dosen.dashboard');
    
    Route::prefix('krs')->group(function () {
        Route::get('/', [DosenKrsApproval::class, 'index'])->name('dosen.krs.index');
        Route::get('{krs}', [DosenKrsApproval::class, 'detail'])->name('dosen.krs.detail');
        Route::post('{krs}/approve', [DosenKrsApproval::class, 'approve'])->name('dosen.krs.approve');
        Route::post('{krs}/reject', [DosenKrsApproval::class, 'reject'])->name('dosen.krs.reject');
    });
    
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('dosen.absensi.index');
        Route::get('{kelas}/edit', [AbsensiController::class, 'edit'])->name('dosen.absensi.edit');
        Route::post('{kelas}/update', [AbsensiController::class, 'update'])->name('dosen.absensi.update');
    });
    
    Route::prefix('nilai')->group(function () {
        Route::get('/', [NilaiController::class, 'index'])->name('dosen.nilai.index');
        Route::get('{kelas}/edit', [NilaiController::class, 'edit'])->name('dosen.nilai.edit');
        Route::post('{kelas}/update', [NilaiController::class, 'update'])->name('dosen.nilai.update');
        Route::get('{kelas}/export-pdf', [NilaiController::class, 'exportPDF'])->name('dosen.nilai.export-pdf');
    });
});

// Mahasiswa Routes
Route::middleware(['auth', 'role:mahasiswa'])->prefix('mahasiswa')->group(function () {
    Route::get('dashboard', [MahasiswaDashboard::class, 'index'])->name('mahasiswa.dashboard');
    
    Route::prefix('krs')->group(function () {
        Route::get('create', [KrsController::class, 'create'])->name('mahasiswa.krs.create');
        Route::post('store', [KrsController::class, 'store'])->name('mahasiswa.krs.store');
        Route::get('{krs}', [KrsController::class, 'show'])->name('mahasiswa.krs.show');
    });
    
    Route::prefix('pembayaran')->group(function () {
        Route::get('/', [MahasiswaPembayaran::class, 'index'])->name('mahasiswa.pembayaran.index');
        Route::get('create', [MahasiswaPembayaran::class, 'create'])->name('mahasiswa.pembayaran.create');
        Route::post('store', [MahasiswaPembayaran::class, 'store'])->name('mahasiswa.pembayaran.store');
    });
    
    Route::prefix('nilai')->group(function () {
        Route::get('/', [MahasiswaNilai::class, 'index'])->name('mahasiswa.nilai.index');
        Route::get('khs/export-pdf', [MahasiswaNilai::class, 'exportPDF'])->name('mahasiswa.nilai.export-pdf');
    });
});