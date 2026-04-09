<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\SketsaController;

Route::middleware(['auth'])->group(function () {
    // Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

    // Kegiatan
    Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::put('/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

     // Wilayah
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::post('/wilayah', [WilayahController::class, 'store'])->name('wilayah.store');
    Route::put('/wilayah/{wilayah}', [WilayahController::class, 'update'])->name('wilayah.update');
    Route::delete('/wilayah/{wilayah}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');

     // Sketsa
    Route::get('/sketsa/wa',  [SketsaController::class, 'wa'])->name('sketsa.wa');
    Route::get('/sketsa/wb',  [SketsaController::class, 'wb'])->name('sketsa.wb');
    Route::get('/sketsa/sls', [SketsaController::class, 'sls'])->name('sketsa.sls');
    Route::post('/sketsa',    [SketsaController::class, 'store'])->name('sketsa.store');
    Route::put('/sketsa/{peta}',    [SketsaController::class, 'update'])->name('sketsa.update');
    Route::delete('/sketsa/{peta}', [SketsaController::class, 'destroy'])->name('sketsa.destroy');
    Route::post('/sketsa/{peta}/download', [SketsaController::class, 'download'])->name('sketsa.download');

    // AJAX
    Route::get('/api/desa-by-kec',      [SketsaController::class, 'getDesaByKec'])->name('api.desa');
    Route::get('/api/sls-by-wilayah',   [SketsaController::class, 'getSlsByWilayah'])->name('api.sls');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
