<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\WDController;
use App\Models\User;

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

//test surat doang
Route::get('/surat-aktif', function () {
    return view('template.surat-aktif-kuliah', [
        'mahasiswa' =>  User::where('username', 'G1A020036')->first()
    ]);
});


Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'create']);
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authLogin');
});

Route::middleware('auth')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->middleware('userAccess:1');
        Route::get('/users/mahasiswa', [MahasiswaController::class, 'index'])->middleware('userAccess:1')->name('admin-mahasiswa');
        Route::get('/users/mahasiswa/new', [MahasiswaController::class, 'create'])->middleware('userAccess:1')->name('admin-mahasiswa-create');
        Route::get('/users/staff', [StaffController::class, 'index'])->middleware('userAccess:1')->name('admin-staff');
        Route::get('/users/kaprodi', [KaprodiController::class, 'index'])->middleware('userAccess:1')->name('admin-kaprodi');
        Route::get('/users/wd', [WDController::class, 'index'])->middleware('userAccess:1')->name('admin-wd');
        Route::get('/surat', [SuratController::class, 'index'])->middleware('userAccess:1');
    });
    Route::prefix('mahasiswa')->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard'])->middleware('userAccess:2');
        Route::get('/pengajuan-surat', [MahasiswaController::class, 'pengajuanSurat'])->middleware('userAccess:2');
        Route::get('/pengajuan-surat/{jenisSurat}', [SuratController::class, 'create'])->middleware('userAccess:2')->name('show-form-surat');
        Route::post('/pengajuan-surat/store/6', [SuratController::class, 'storeSuratKeteranganAlumni'])->middleware('userAccess:2')->name('store-surat-alumni');
        Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->middleware('userAccess:2')->name('redirect-form-surat');
        Route::get('/riwayat-pengajuan-surat', [MahasiswaController::class, 'riwayatPengajuanSurat'])->middleware('userAccess:2');
        Route::get('/lacak-surat', [MahasiswaController::class, 'lacakSurat'])->middleware('userAccess:2');
    });
    Route::prefix('staff')->group(function () {

        Route::get('/', [StaffController::class, 'dashboard'])->middleware('userAccess:3');
        Route::get('/surat-masuk', [StaffController::class, 'suratMasuk'])->middleware('userAccess:3');
        Route::get('/surat-disetujui', [StaffController::class, 'suratDisetujui'])->middleware('userAccess:3');
        Route::put('/surat-disetujui/{surat}', [StaffController::class, 'setujuiSurat'])->middleware('userAccess:3')->name('setujui-surat');
        Route::get('/surat-ditolak/{surat}', [StaffController::class, 'confirmTolakSurat'])->middleware('userAccess:3')->name('confirm-tolak-surat');
        Route::put('/surat-ditolak/{surat}', [StaffController::class, 'tolakSurat'])->middleware('userAccess:3')->name('tolak-surat');
    });
    Route::prefix('kaprodi')->group(function () {

        Route::get('/', [KaprodiController::class, 'dashboard'])->middleware('userAccess:4');
        Route::get('/surat-masuk', [KaprodiController::class, 'suratMasuk'])->middleware('userAccess:4');
        Route::get('/surat-disetujui', [KaprodiController::class, 'suratDisetujui'])->middleware('userAccess:4');
        Route::put('/surat-disetujui/{surat}', [KaprodiController::class, 'setujuiSurat'])->middleware('userAccess:4')->name('setujui-surat-kaprodi');
        Route::get('/surat-ditolak/{surat}', [KaprodiController::class, 'confirmTolakSurat'])->middleware('userAccess:4')->name('confirm-tolak-surat-kaprodi');
        Route::put('/surat-ditolak/{surat}', [KaprodiController::class, 'tolakSurat'])->middleware('userAccess:4')->name('tolak-surat-kaprodi');
    });
    Route::prefix('wd')->group(function () {

        Route::get('/', [WDController::class, 'dashboard'])->middleware('userAccess:5');
        Route::get('/surat-masuk', [WDController::class, 'suratMasuk'])->middleware('userAccess:5');
        Route::get('/surat-disetujui', [WDController::class, 'suratDisetujui'])->middleware('userAccess:5');
        Route::put('/surat-disetujui/{surat}', [WDController::class, 'setujuiSurat'])->middleware('userAccess:5')->name('setujui-surat-wd');
        Route::get('/surat-ditolak/{surat}', [WDController::class, 'confirmTolakSurat'])->middleware('userAccess:5')->name('confirm-tolak-surat-wd');
        Route::put('/surat-ditolak/{surat}', [WDController::class, 'tolakSurat'])->middleware('userAccess:5')->name('tolak-surat-wd');
    });
});
Route::get('/home', function () {
    return redirect('/admin');
});
Route::get('/login', function () {
    return redirect('/');
});
