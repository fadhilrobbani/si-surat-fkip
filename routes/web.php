<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WDController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\JenisSuratController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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
Route::get('/print-test/{surat}', [PDFController::class, 'liveTest']);
Route::get('/send-surat/{surat}', [EmailController::class, 'send']);

Route::get('/home', [AuthController::class, 'home']);
Route::get('/login', function () {
    return redirect('/');
});
Route::get('/register', [AuthController::class, 'create']);
Route::post('/register/new', [AuthController::class, 'store'])->name('register-user');
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

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
        Route::get('/users/akademik', [AkademikController::class, 'index'])->middleware('userAccess:1')->name('admin-akademik');
        Route::get('/surat', [SuratController::class, 'index'])->middleware('userAccess:1');
    });
    Route::prefix('mahasiswa')->middleware('verified')->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard'])->middleware('userAccess:2');
        Route::get('/pengajuan-surat', [MahasiswaController::class, 'pengajuanSurat'])->middleware('userAccess:2');
        Route::get('/pengajuan-surat/{jenisSurat}', [SuratController::class, 'create'])->middleware('userAccess:2')->name('show-form-surat');
        Route::post('/pengajuan-surat/store/6', [SuratController::class, 'storeSuratKeteranganAlumni'])->middleware('userAccess:2')->name('store-surat-alumni');
        Route::post('/pengajuan-surat/store/8', [SuratController::class, 'storeSuratKeteranganLulus'])->middleware('userAccess:2')->name('store-surat-lulus');
        Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->middleware('userAccess:2')->can('mahasiswaCanCancelSurat', 'surat')->name('destroy-surat');
        Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->middleware('userAccess:2')->name('redirect-form-surat');
        Route::get('/riwayat-pengajuan-surat', [MahasiswaController::class, 'riwayatPengajuanSurat'])->middleware('userAccess:2');
        Route::get('/riwayat-pengajuan-surat/show/{surat}', [MahasiswaController::class, 'lihatSurat'])->middleware('userAccess:2')->can('mahasiswaCanViewShowRiwayatPengajuanSurat', 'surat')->name('lihat-surat-mahasiswa');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('mahasiswaCanPrintSurat', 'surat')->name('print-surat-mahasiswa');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('mahasiswaCanShowLampiranSurat', 'surat')->name('show-file-mahasiswa');
    });
    Route::prefix('staff')->group(function () {

        Route::get('/', [StaffController::class, 'dashboard'])->middleware('userAccess:3');
        Route::get('/surat-masuk', [StaffController::class, 'suratMasuk'])->middleware('userAccess:3');
        Route::get('/surat-masuk/show/{surat}', [StaffController::class, 'showSuratMasuk'])->middleware('userAccess:3')->can('staffCanShowSuratMasuk', 'surat')->name('show-surat-staff');
        Route::get('/riwayat-persetujuan', [StaffController::class, 'riwayatPersetujuan'])->middleware('userAccess:3');
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffController::class, 'showApproval'])->middleware('userAccess:3')->can('staffCanShowRiwayatPersetujuan', 'approval')->name('show-approval-staff');
        Route::put('/surat-disetujui/{surat}', [StaffController::class, 'setujuiSurat'])->middleware('userAccess:3')->can('staffCanApproveSuratMasuk', 'surat')->name('setujui-surat');
        Route::get('/surat-ditolak/{surat}', [StaffController::class, 'confirmTolakSurat'])->middleware('userAccess:3')->can('staffCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat');
        Route::put('/surat-ditolak/{surat}', [StaffController::class, 'tolakSurat'])->middleware('userAccess:3')->can('staffCanDenySuratMasuk', 'surat')->name('tolak-surat');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('staffCanPrintSurat', 'surat')->name('print-surat-staff');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('staffCanShowLampiranSurat', 'surat')->name('show-file-staff');
    });
    Route::prefix('kaprodi')->group(function () {

        Route::get('/', [KaprodiController::class, 'dashboard'])->middleware('userAccess:4');
        Route::get('/surat-masuk', [KaprodiController::class, 'suratMasuk'])->middleware('userAccess:4');
        Route::get('/surat-masuk/show/{surat}', [KaprodiController::class, 'showSuratMasuk'])->middleware('userAccess:4')->can('kaprodiCanShowSuratMasuk', 'surat')->name('show-surat-kaprodi');
        Route::get('/riwayat-persetujuan', [KaprodiController::class, 'riwayatPersetujuan'])->middleware('userAccess:4');
        Route::get('/riwayat-persetujuan/show/{approval}', [KaprodiController::class, 'showApproval'])->middleware('userAccess:4')->can('kaprodiCanShowRiwayatPersetujuan', 'approval')->name('show-approval-kaprodi');
        Route::put('/surat-disetujui/{surat}', [KaprodiController::class, 'setujuiSurat'])->middleware('userAccess:4')->can('kaprodiCanApproveSuratMasuk', 'surat')->name('setujui-surat-kaprodi');
        Route::get('/surat-ditolak/{surat}', [KaprodiController::class, 'confirmTolakSurat'])->middleware('userAccess:4')->can('kaprodiCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat-kaprodi');
        Route::put('/surat-ditolak/{surat}', [KaprodiController::class, 'tolakSurat'])->middleware('userAccess:4')->can('kaprodiCanDenySuratMasuk', 'surat')->name('tolak-surat-kaprodi');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('kaprodiCanPrintSurat', 'surat')->name('print-surat-kaprodi');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('kaprodiCanShowLampiranSurat', 'surat')->name('show-file-kaprodi');
    });
    Route::prefix('wd')->group(function () {
        Route::get('/', [WDController::class, 'dashboard'])->middleware('userAccess:5');
        Route::get('/surat-masuk', [WDController::class, 'suratMasuk'])->middleware('userAccess:5');
        Route::get('/riwayat-persetujuan', [WDController::class, 'riwayatPersetujuan'])->middleware('userAccess:5');
        Route::get('/riwayat-persetujuan/show/{approval}', [WDController::class, 'showApproval'])->middleware('userAccess:5')->can('wdCanShowRiwayatPersetujuan', 'approval')->name('show-approval-wd');
        Route::get('/surat-masuk/show/{surat}', [WDController::class, 'showSuratMasuk'])->middleware('userAccess:5')->name('show-surat-wd');
        Route::put('/surat-disetujui/{surat}', [WDController::class, 'setujuiSurat'])->middleware('userAccess:5')->name('setujui-surat-wd');
        Route::get('/surat-ditolak/{surat}', [WDController::class, 'confirmTolakSurat'])->middleware('userAccess:5')->name('confirm-tolak-surat-wd');
        Route::put('/surat-ditolak/{surat}', [WDController::class, 'tolakSurat'])->middleware('userAccess:5')->name('tolak-surat-wd');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-wd');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-wd');
    });
    Route::prefix('akademik')->group(function () {
        Route::get('/', [AkademikController::class, 'dashboard'])->middleware('userAccess:6');
        Route::get('/surat-masuk', [AkademikController::class, 'suratMasuk'])->middleware('userAccess:6');
        Route::get('/riwayat-persetujuan', [AkademikController::class, 'riwayatPersetujuan'])->middleware('userAccess:6');
        Route::get('/riwayat-persetujuan/show/{approval}', [AkademikController::class, 'showApproval'])->middleware('userAccess:6')->can('akademikCanShowRiwayatPersetujuan', 'approval')->name('show-approval-akademik');
        Route::get('/surat-masuk/show/{surat}', [AkademikController::class, 'showSuratMasuk'])->middleware('userAccess:6')->can('akademikCanShowSuratMasuk', 'surat')->name('show-surat-akademik');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->middleware('userAccess:6')->can('akademikCanShowPreviewSuratMasuk', 'surat')->name('preview-surat');
        Route::put('/surat-disetujui/{surat}', [AkademikController::class, 'setujuiSurat'])->middleware('userAccess:6')->can('akademikCanApproveSuratMasuk', 'surat')->name('setujui-surat-akademik');
        Route::get('/surat-ditolak/{surat}', [AkademikController::class, 'confirmTolakSurat'])->middleware('userAccess:6')->can('akademikCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat-akademik');
        Route::put('/surat-ditolak/{surat}', [AkademikController::class, 'tolakSurat'])->middleware('userAccess:6')->can('akademikCanDenySuratMasuk', 'surat')->name('tolak-surat-akademik');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('akademikCanPrintSurat', 'surat')->name('print-surat-akademik');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('akademikCanShowLampiranSurat', 'surat')->name('show-file-akademik');
    });
});
