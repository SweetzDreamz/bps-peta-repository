<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\SketsaController;
use App\Http\Controllers\HistoryController;

// =============================================
// Route publik
// =============================================
Route::get('/', function () {
    return view('welcome');
});

// =============================================
// Route semua role
// =============================================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Peta (sebelumnya Sketsa) semua role bisa akses
    Route::get('/peta/wa',  [SketsaController::class, 'wa'])->name('peta.wa');
    Route::get('/peta/sls', [SketsaController::class, 'sls'])->name('peta.sls');
    Route::post('/peta',    [SketsaController::class, 'store'])->name('peta.store');
    Route::put('/peta/{peta}',    [SketsaController::class, 'update'])->name('peta.update');
    Route::delete('/peta/{peta}', [SketsaController::class, 'destroy'])->name('peta.destroy');
    Route::post('/peta/{peta}/download', [SketsaController::class, 'download'])->name('peta.download');

    // API untuk dropdown SLS all
    Route::get('/api/sls-all', [SketsaController::class, 'getAllSls'])->name('api.sls.all');

    // Riwayat (sebelumnya History) - semua akses
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat.index');
    Route::post('/riwayat/{transaksi}/kembalikan', [HistoryController::class, 'kembalikan'])->name('riwayat.kembalikan');
    Route::delete('/riwayat/{transaksi}/batalkan', [HistoryController::class, 'batalkan'])->name('riwayat.batalkan');

    // AJAX
    Route::get('/api/desa-by-kec',    [SketsaController::class, 'getDesaByKec'])->name('api.desa');
    Route::get('/api/sls-by-wilayah', [SketsaController::class, 'getSlsByWilayah'])->name('api.sls');
    Route::get('/api/sls-by-des',     [SketsaController::class, 'getSlsByDes'])->name('api.sls.by.des');

    // File peta — proteksi akses langsung
    Route::get('/file/peta/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);
        return response()->file($fullPath, [
            'Content-Type'           => mime_content_type($fullPath),
            'Content-Disposition'    => 'inline',
            'Cache-Control'          => 'no-store, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    })->where('path', '.*')->name('file.peta');

});

// =============================================
// Route KHUSUS SUPERVISOR
// Operator yang akses langsung via URL → 403
// =============================================
Route::middleware(['auth', 'supervisor'])->group(function () {

    // Wilayah
    Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('/wilayah/{wilayah}', [WilayahController::class, 'show'])->name('wilayah.show');
    Route::put('/wilayah/{wilayah}', [WilayahController::class, 'update'])->name('wilayah.update');
    Route::delete('/wilayah/{wilayah}', [WilayahController::class, 'destroy'])->name('wilayah.destroy');
    Route::post('/wilayah/import', [WilayahController::class, 'import'])->name('wilayah.import');
    Route::post('/wilayah/gabung', [WilayahController::class, 'gabung'])->name('wilayah.gabung');
    Route::post('/wilayah/pecah-pilih', [WilayahController::class, 'pecah'])->name('wilayah.pecah');
    Route::post('/wilayah/{wilayah}/status-asal', [WilayahController::class, 'updateStatusAsal'])->name('wilayah.status-asal');
    Route::post('/wilayah/pilihan/toggle', [WilayahController::class, 'togglePilihan'])->name('wilayah.pilihan.toggle');
    Route::post('/wilayah/pilihan/clear', [WilayahController::class, 'clearPilihan'])->name('wilayah.pilihan.clear');
    Route::get('/wilayah/pilihan/list', [WilayahController::class, 'getPilihan'])->name('wilayah.pilihan.list');
    Route::get('/wilayah/desa-by-kec', [WilayahController::class, 'getDesaByKec'])->name('wilayah.desa');

    // Kegiatan
    Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::put('/kegiatan/{kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{kegiatan}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

    // Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');



});

require __DIR__.'/auth.php';