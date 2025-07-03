<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SiswaLoginController;
use App\Http\Controllers\InfaqGedungController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

// Resource Routes
Route::resource('/dashboard/data-siswa', SiswaController::class)->names([
    'index' => 'data-siswa.index',
    'create' => 'data-siswa.create',
    'store' => 'data-siswa.store',
    'show' => 'data-siswa.show',
    'edit' => 'data-siswa.edit',
    'update' => 'data-siswa.update',
    'destroy' => 'data-siswa.destroy'
]);
// Route::get('/get-spp/{kelas}', [SiswaController::class, 'getSppByKelas'])->name('get.spp');
Route::resource('/dashboard/data-kelas', KelasController::class);
Route::resource('/dashboard/data-spp', SppController::class);
Route::resource('/dashboard/data-petugas', PetugasController::class);
Route::resource('/dashboard/pembayaran', PembayaranController::class)->names([
    'index' => 'entry-pembayaran.index',
    'create' => 'entry-pembayaran.create',
    'store' => 'entry-pembayaran.store',
    'show' => 'entry-pembayaran.show',
    'edit' => 'entry-pembayaran.edit',
    'update' => 'entry-pembayaran.update',
    'destroy' => 'entry-pembayaran.destroy'
]);

Route::resource('/dashboard/infaq-gedung', InfaqGedungController::class)->names([
    'index' => 'infaq-gedung.index',
    'create' => 'infaq-gedung.create',
    'store' => 'infaq-gedung.store',
    'edit' => 'infaq-gedung.edit',
    'update' => 'infaq-gedung.update',
    'destroy' => 'infaq-gedung.destroy',
]);

Route::resource('/dashboard/histori', HistoryController::class);

// Laporan Routes
Route::get('/dashboard/laporan', [LaporanController::class, 'index']);
Route::get('/dashboard/laporan/create', [LaporanController::class, 'create']);

// Siswa Login Routes
Route::get('/login/siswa', [SiswaLoginController::class, 'siswaLogin']);
Route::post('/login/siswa/proses', [SiswaLoginController::class, 'login']);
Route::get('/dashboard/siswa/histori', [SiswaLoginController::class, 'index']);
Route::get('/siswa/logout', [SiswaLoginController::class, 'logout']);