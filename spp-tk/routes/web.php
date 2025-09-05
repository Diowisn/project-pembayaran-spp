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
use App\Http\Controllers\UangTahunanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KegiatanSiswaController;
use App\Http\Controllers\InklusiController;
use App\Http\Controllers\KegiatanTahunanController;
// use App\Models\Tabungan;

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

Route::resource('/dashboard/data-siswa', SiswaController::class)->names([
    'index' => 'data-siswa.index',
    'create' => 'data-siswa.create',
    'store' => 'data-siswa.store',
    'show' => 'data-siswa.show',
    'edit' => 'data-siswa.edit',
    'update' => 'data-siswa.update',
    'destroy' => 'data-siswa.destroy'
]);

Route::resource('/dashboard/data-inklusi', InklusiController::class);
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
Route::get('/pembayaran/{id}/generate-pdf', [PembayaranController::class, 'generate'])->name('pembayaran.generate-pdf');
Route::get('/pembayaran/siswa/{siswaId}/rekap-pdf', [PembayaranController::class, 'generateRiwayat'])->name('pembayaran.rekap-siswa');

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

Route::resource('/dashboard/data-kegiatan-tahunan', KegiatanTahunanController::class)->names([
    'index' => 'data-kegiatan-tahunan.index',
    'create' => 'data-kegiatan-tahunan.create',
    'store' => 'data-kegiatan-tahunan.store',
    'edit' => 'data-kegiatan-tahunan.edit',
    'update' => 'data-kegiatan-tahunan.update',
    'destroy' => 'data-kegiatan-tahunan.destroy',
]);

// Routes untuk Kegiatan Siswa
Route::resource('/dashboard/entri-kegiatan', KegiatanSiswaController::class)->names([
    'index' => 'entri-kegiatan.index',
    'store' => 'entri-kegiatan.store',
    // 'edit' => 'entri-kegiatan.edit',
    // 'update' => 'entri-kegiatan.update',
    'destroy' => 'entri-kegiatan.destroy',
])->except(['create', 'show', 'edit', 'update']);

Route::get('entri-kegiatan/{id}/edit', [KegiatanSiswaController::class, 'edit'])->name('entri-kegiatan.edit');
Route::put('entri-kegiatan/{id}', [KegiatanSiswaController::class, 'update'])->name('entri-kegiatan.update');
Route::get('/dashboard/entri-kegiatan/cari-siswa', [KegiatanSiswaController::class, 'cariSiswa'])
    ->name('entri-kegiatan.cari-siswa');
Route::post('/dashboard/entri-kegiatan/{siswaId}/toggle-partisipasi', [KegiatanSiswaController::class, 'togglePartisipasi'])
    ->name('entri-kegiatan.toggle-partisipasi');
Route::post('/entri-kegiatan/bayar-semua', [KegiatanSiswaController::class, 'bayarSemua'])
    ->name('entri-kegiatan.bayar-semua');
Route::get('/entri-kegiatan/generate-pdf/{id}', [KegiatanSiswaController::class, 'generatePDF'])
    ->name('entri-kegiatan.generate-pdf');
Route::get('/entri-kegiatan/generate-rekap-siswa-pdf', [KegiatanSiswaController::class, 'generateRekapSiswaPDF'])
    ->name('entri-kegiatan.generate-rekap-siswa-pdf');

Route::get('history-kegiatan', [HistoryController::class, 'kegiatan'])->name('history-kegiatan.index');
Route::get('history-kegiatan/{id}', [HistoryController::class, 'showKegiatan'])->name('history-kegiatan.show');
Route::delete('history-kegiatan/{id}', [HistoryController::class, 'destroyKegiatan'])->name('history-kegiatan.destroy');
    
Route::resource('/dashboard/kegiatan', KegiatanController::class);
Route::get('/dashboard/entri-kegiatan', [KegiatanSiswaController::class, 'index'])->name('entri-kegiatan.index');
Route::get('/dashboard/entri-kegiatan/cari-siswa', [KegiatanSiswaController::class, 'cariSiswa'])->name('entri-kegiatan.cari-siswa');
Route::post('/dashboard/entri-kegiatan/{siswaId}/update-partisipasi', [KegiatanSiswaController::class, 'updatePartisipasi'])->name('entri-kegiatan.update-partisipasi');
Route::post('/dashboard/entri-kegiatan/{id}/update-status-bayar', [KegiatanSiswaController::class, 'updateStatusBayar'])->name('entri-kegiatan.update-status-bayar');
Route::get('/dashboard/entri-kegiatan/{id}/edit', [KegiatanSiswaController::class, 'edit'])->name('entri-kegiatan.edit');
Route::put('/dashboard/entri-kegiatan/{id}', [KegiatanSiswaController::class, 'update'])->name('entri-kegiatan.update');
Route::delete('/dashboard/entri-kegiatan/{id}', [KegiatanSiswaController::class, 'destroy'])->name('entri-kegiatan.destroy');

Route::get('/history-pembayaran', [HistoryController::class, 'index'])->name('history-pembayaran.index');
Route::get('/history-pembayaran/{id}', [HistoryController::class, 'show'])->name('history-pembayaran.show');

Route::get('histori-infaq', [HistoryController::class, 'infaq'])->name('histori.infaq');
Route::get('/history-tabungan', [TabunganController::class, 'histori'])->name('histori.tabungan');
Route::get('/dashboard/history/uang-tahunan', [HistoryController::class, 'uangTahunan'])->name('history.uang-tahunan');

// Laporan Routes
Route::get('/dashboard/laporan', [LaporanController::class, 'index']);
Route::get('/dashboard/laporan/create', [LaporanController::class, 'create']);

Route::get('/pembayaran/{id}/generate', [PembayaranController::class, 'generate'])->name('pembayaran.generate');
Route::get('/infaq/{id}/generate', [InfaqController::class, 'generate'])->name('infaq.generate');

// Siswa Login Routes
Route::get('/login/siswa', [SiswaLoginController::class, 'siswaLogin'])->name('login.siswa');
Route::post('/login/siswa/proses', [SiswaLoginController::class, 'login']);
Route::get('/siswa/logout', [SiswaLoginController::class, 'logout']);

Route::get('/dashboard/siswa', [SiswaLoginController::class, 'dashboard'])->name('siswa.dashboard');
Route::get('/dashboard/siswa/histori', [SiswaLoginController::class, 'index']);
Route::get('/dashboard/siswa/infaq', [SiswaLoginController::class, 'infaq']);
Route::get('/dashboard/siswa/tabungan', [SiswaLoginController::class, 'tabungan'])->name('siswa.tabungan');
Route::get('dashboard/siswa/uang-tahunan', [SiswaLoginController::class, 'uangTahunan'])->name('siswa.uang-tahunan');

Route::get('/siswa/pembayaran/{id}/cetak', [SiswaInfaqController::class, 'generateSpp'])->name('siswa.pembayaran.cetak');
Route::get('/siswa/infaq/{id}/cetak', [SiswaInfaqController::class, 'generateInfaq'])->name('siswa.infaq.cetak');
Route::get('/siswa/tabungan/cetak', [SiswaInfaqController::class, 'generateTabungan'])->name('siswa.tabungan.cetak');
Route::get('/siswa/uang-tahunan/cetak', [SiswaInfaqController::class, 'generateUangTahunan'])->name('siswa.uang-tahunan.cetak');

Route::put('/profile/update', [SiswaLoginController::class, 'updateProfile'])->name('siswa.profile.update');
Route::get('/get-data', [SiswaLoginController::class, 'getData'])->name('siswa.get-data');

Route::get('/dashboard/tabungan', [TabunganController::class, 'index'])->name('tabungan.index');
Route::get('/dashboard/tabungan/create', [TabunganController::class, 'create'])->name('tabungan.create');
Route::post('/dashboard/tabungan/store-manual', [TabunganController::class, 'storeManual'])->name('tabungan.store-manual');
Route::get('/dashboard/tabungan/{id}', [TabunganController::class, 'show'])->name('tabungan.show');
Route::get('/dashboard/tabungan/{id}/edit', [TabunganController::class, 'edit'])->name('tabungan.edit');
Route::put('/dashboard/tabungan/{id}/update', [TabunganController::class, 'update'])->name('tabungan.update');
Route::post('/dashboard/tabungan/tarik/{id}', [TabunganController::class, 'tarik'])->name('tabungan.tarik');
Route::get('/dashboard/tabungan/report/{id}', [TabunganController::class, 'generateReport'])->name('tabungan.report');
Route::delete('/dashboard/tabungan/{id}', [TabunganController::class, 'destroy'])->name('tabungan.destroy');

Route::get('/dashboard/uang-tahunan', [UangTahunanController::class, 'index'])->name('uang-tahunan.index');
// Route::get('/dashboard/uang-tahunan/create', [UangTahunanController::class, 'create'])->name('uang-tahunan.create');
Route::post('/dashboard/uang-tahunan/store-manual', [UangTahunanController::class, 'storeManual'])->name('uang-tahunan.store-manual');
Route::get('/dashboard/uang-tahunan/{id}', [UangTahunanController::class, 'show'])->name('uang-tahunan.show');
Route::get('/dashboard/uang-tahunan/{id}/edit', [UangTahunanController::class, 'edit'])->name('uang-tahunan.edit');
Route::put('/dashboard/uang-tahunan/{id}/update', [UangTahunanController::class, 'update'])->name('uang-tahunan.update');
Route::post('/dashboard/uang-tahunan/tarik/{id}', [UangTahunanController::class, 'tarik'])->name('uang-tahunan.tarik');
Route::get('/dashboard/uang-tahunan/report/{id}/{tahun}', [UangTahunanController::class, 'generateReport'])->name('uang-tahunan.report');
Route::delete('/dashboard/uang-tahunan/{id}', [UangTahunanController::class, 'destroy'])->name('uang-tahunan.destroy');
Route::get('/dashboard/uang-tahunan/cari', [UangTahunanController::class, 'cariSiswa'])->name('uang-tahunan.cari-siswa');

// Di routes/web.php tambahkan sementara:
// Route::get('/test-tabungan', function() {
//     $tabungan = Tabungan::create([
//         'id_siswa' => 1, // Ganti dengan ID siswa yang ada
//         'id_petugas' => 1, // Ganti dengan ID petugas yang ada
//         'debit' => 100000,
//         'kredit' => 0,
//         'saldo' => 100000,
//         'keterangan' => 'Data testing manual'
//     ]);
    
//     return 'Tabungan test created: '.$tabungan->id;
// });

?>