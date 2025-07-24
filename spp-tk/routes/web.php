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
use App\Http\Controllers\InfaqController;
use App\Http\Controllers\SiswaInfaqController;
use App\Http\Controllers\TabunganController;
use App\Models\Tabungan;

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
    return view('auth.login-option');
})->name('login.option');

Auth::routes();

Route::get('/login-option', function () {
    return view('auth.login-option');
})->name('login.option');

Route::get('/login/admin', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
    ->name('login.admin');

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

Route::resource('/dashboard/data-kelas', KelasController::class);
Route::resource('/dashboard/data-spp', SppController::class);
Route::resource('/dashboard/data-petugas', PetugasController::class);

Route::get('/dashboard/pembayaran/cari', [PembayaranController::class, 'cariSiswa'])->name('pembayaran.cari-siswa');

Route::resource('/dashboard/pembayaran', PembayaranController::class)->names([
    'index' => 'entry-pembayaran.index',
    'create' => 'entry-pembayaran.create',
    'store' => 'entry-pembayaran.store',
    'show' => 'entry-pembayaran.show',
    'edit' => 'entry-pembayaran.edit',
    'update' => 'entry-pembayaran.update',
    'destroy' => 'entry-pembayaran.destroy'
]);

Route::get('/dashboard/infaq/cari', [InfaqController::class, 'cariSiswa'])->name('infaq.cari-siswa');

Route::resource('/dashboard/infaq', InfaqController::class)->names([
    'index' => 'infaq.index',
    'create' => 'infaq.create',
    'store' => 'infaq.store',
    'show' => 'infaq.show',
    'edit' => 'infaq.edit',
    'update' => 'infaq.update',
    'destroy' => 'infaq.destroy'
]);

Route::resource('/dashboard/infaq-gedung', InfaqGedungController::class)->names([
    'index' => 'infaq-gedung.index',
    'create' => 'infaq-gedung.create',
    'store' => 'infaq-gedung.store',
    'edit' => 'infaq-gedung.edit',
    'update' => 'infaq-gedung.update',
    'destroy' => 'infaq-gedung.destroy',
]);

Route::get('histori', [HistoryController::class, 'index'])->name('histori.spp');
Route::get('histori-infaq', [HistoryController::class, 'infaq'])->name('histori.infaq');

// Laporan Routes
Route::get('/dashboard/laporan', [LaporanController::class, 'index']);
Route::get('/dashboard/laporan/create', [LaporanController::class, 'create']);

Route::get('/pembayaran/{id}/generate', [PembayaranController::class, 'generate'])->name('pembayaran.generate');
Route::get('/infaq/{id}/generate', [InfaqController::class, 'generate'])->name('infaq.generate');

// Siswa Login Routes
Route::get('/login/siswa', [SiswaLoginController::class, 'siswaLogin'])->name('login.siswa');
Route::post('/login/siswa/proses', [SiswaLoginController::class, 'login']);
Route::get('/siswa/logout', [SiswaLoginController::class, 'logout']);

Route::get('/dashboard/siswa/histori', [SiswaLoginController::class, 'index']);
Route::get('/dashboard/siswa/infaq', [SiswaLoginController::class, 'infaq']);

Route::get('/siswa/pembayaran/{id}/cetak', [SiswaInfaqController::class, 'generateSpp'])->name('siswa.pembayaran.cetak');
Route::get('/siswa/infaq/{id}/cetak', [SiswaInfaqController::class, 'generateInfaq'])->name('siswa.infaq.cetak');

Route::put('/profile/update', [SiswaLoginController::class, 'updateProfile'])->name('siswa.profile.update');
Route::get('/get-data', [SiswaLoginController::class, 'getData'])->name('siswa.get-data');

Route::get('/dashboard/tabungan', [TabunganController::class, 'index'])->name('tabungan.index');
Route::get('/dashboard/tabungan/{id}', [TabunganController::class, 'show'])->name('tabungan.show');
Route::post('/dashboard/tabungan/tarik/{id}', [TabunganController::class, 'tarik'])->name('tabungan.tarik');
Route::get('/dashboard/tabungan/report/{id}', [TabunganController::class, 'generateReport'])->name('tabungan.report');
// Di routes/web.php tambahkan sementara:
Route::get('/test-tabungan', function() {
    $tabungan = Tabungan::create([
        'id_siswa' => 1, // Ganti dengan ID siswa yang ada
        'id_petugas' => 1, // Ganti dengan ID petugas yang ada
        'debit' => 100000,
        'kredit' => 0,
        'saldo' => 100000,
        'keterangan' => 'Data testing manual'
    ]);
    
    return 'Tabungan test created: '.$tabungan->id;
});