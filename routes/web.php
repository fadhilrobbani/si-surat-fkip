<?php

use App\Events\NotificationCreated;
use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

Route::get('testnotif', function () {
    NotificationCreated::dispatch('woi');
    return view('previews.show-surat-qr');
});

Route::get('/home', [AuthController::class, 'home']);
Route::get('/login', function () {
    return redirect('/');
});

Route::get('/surat-terverifikasi/{surat}', [SuratController::class, 'previewSuratQR'])->middleware('signed')->name('preview-surat-qr');
Route::get('/surat-terverifikasi/{surat}/cetak', [PDFController::class, 'previewSurat'])->middleware('signed')->name('cetak-surat-qr');
Route::get('/register', [AuthController::class, 'create']);
Route::post('/register/new', [AuthController::class, 'store'])->name('register-user');
Route::get('/email/verify', function () {
    Gate::allowIf(fn (User $user) => $user->email_verified_at == null);
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification/{user}', [AuthController::class, 'emailVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/forgot-password', [AuthController::class, 'forgotPasswordPage'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordPage'])->name('password.reset');


Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'create']);
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authLogin');
});
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('mahasiswa')->middleware(['userAccess:2'])->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard']);
        Route::middleware('verified')->group(function () {

            Route::get('/pengajuan-surat', [MahasiswaController::class, 'pengajuanSurat']);
            Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('show-form-surat');
            // Route::post('/pengajuan-surat/store/6', [SuratController::class, 'storeSuratKeteranganAlumni'])->name('store-surat-alumni');
            // Route::post('/pengajuan-surat/store/8', [SuratController::class, 'storeSuratKeteranganLulus'])->name('store-surat-lulus');
            Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'store'])->name('store-surat');
            // Route::get('/pengajuan-surat/{jenisSurat:slug}/template',[PDFController::class,'templateSurat'])->name('template-surat');
            Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->can('mahasiswaCanCancelSurat', 'surat')->name('destroy-surat');
            Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('redirect-form-surat');
            Route::get('/riwayat-pengajuan-surat', [MahasiswaController::class, 'riwayatPengajuanSurat']);
            Route::get('/riwayat-pengajuan-surat/show/{surat}', [MahasiswaController::class, 'lihatSurat'])->can('mahasiswaCanViewShowRiwayatPengajuanSurat', 'surat')->name('lihat-surat-mahasiswa');
            Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('mahasiswaCanPrintSurat', 'surat')->name('print-surat-mahasiswa');
            Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('mahasiswaCanShowLampiranSurat', 'surat')->name('show-file-mahasiswa');
        });
        Route::get('/profile', [MahasiswaController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [MahasiswaController::class, 'updateProfile'])->name('update-profile');
    });
    Route::prefix('staff')->middleware(['userAccess:3'])->group(function () {
        Route::get('/surat-masuk', [StaffController::class, 'suratMasuk'])->name('surat-masuk-staff');
        Route::get('/surat-masuk/show/{surat}', [StaffController::class, 'showSuratMasuk'])->can('staffCanShowSuratMasuk', 'surat')->name('show-surat-staff');
        Route::get('/riwayat-persetujuan', [StaffController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffController::class, 'showApproval'])->can('staffCanShowRiwayatPersetujuan', 'approval')->name('show-approval-staff');
        Route::put('/surat-disetujui/{surat}', [StaffController::class, 'setujuiSurat'])->can('staffCanApproveSuratMasuk', 'surat')->name('setujui-surat-staff');
        Route::get('/surat-ditolak/{surat}', [StaffController::class, 'confirmTolakSurat'])->can('staffCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat-staff');
        Route::put('/surat-ditolak/{surat}', [StaffController::class, 'tolakSurat'])->can('staffCanDenySuratMasuk', 'surat')->name('tolak-surat-staff');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('staffCanPrintSurat', 'surat')->name('print-surat-staff');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('staffCanShowLampiranSurat', 'surat')->name('show-file-staff');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-staff');
        Route::get('/', [StaffController::class, 'dashboard']);
        Route::get('/profile', [StaffController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [StaffController::class, 'updateProfile'])->name('update-profile-staff');
    });
    Route::prefix('kaprodi')->middleware(['userAccess:4'])->group(function () {
        Route::get('/surat-masuk', [KaprodiController::class, 'suratMasuk']);
        Route::get('/surat-masuk/show/{surat}', [KaprodiController::class, 'showSuratMasuk'])->can('kaprodiCanShowSuratMasuk', 'surat')->name('show-surat-kaprodi');
        Route::get('/riwayat-persetujuan', [KaprodiController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [KaprodiController::class, 'showApproval'])->can('kaprodiCanShowRiwayatPersetujuan', 'approval')->name('show-approval-kaprodi');
        Route::put('/surat-disetujui/{surat}', [KaprodiController::class, 'setujuiSurat'])->can('kaprodiCanApproveSuratMasuk', 'surat')->name('setujui-surat-kaprodi');
        Route::get('/surat-ditolak/{surat}', [KaprodiController::class, 'confirmTolakSurat'])->can('kaprodiCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat-kaprodi');
        Route::put('/surat-ditolak/{surat}', [KaprodiController::class, 'tolakSurat'])->can('kaprodiCanDenySuratMasuk', 'surat')->name('tolak-surat-kaprodi');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('kaprodiCanPrintSurat', 'surat')->name('print-surat-kaprodi');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('kaprodiCanShowLampiranSurat', 'surat')->name('show-file-kaprodi');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->can('kaprodiCanShowPreviewSuratMasuk', 'surat')->name('preview-surat-kaprodi');

        Route::get('/', [KaprodiController::class, 'dashboard']);
        Route::get('/profile', [KaprodiController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [KaprodiController::class, 'updateProfile'])->name('update-profile-kaprodi');
    });
    Route::prefix('wd')->middleware(['userAccess:5'])->group(function () {
        Route::get('/surat-masuk', [WDController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [WDController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [WDController::class, 'showApproval'])->can('wdCanShowRiwayatPersetujuan', 'approval')->name('show-approval-wd');
        Route::get('/surat-masuk/show/{surat}', [WDController::class, 'showSuratMasuk'])->name('show-surat-wd');
        Route::put('/surat-disetujui/{surat}', [WDController::class, 'setujuiSurat'])->name('setujui-surat-wd');
        Route::get('/surat-ditolak/{surat}', [WDController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-wd');
        Route::put('/surat-ditolak/{surat}', [WDController::class, 'tolakSurat'])->name('tolak-surat-wd');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-wd');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-wd');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-wd');
        Route::get('/', [WDController::class, 'dashboard']);
        Route::get('/profile', [WDController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [WDController::class, 'updateProfile'])->name('update-profile-wd');
    });
    Route::prefix('akademik')->middleware(['userAccess:6'])->group(function () {
        // Route::middleware('verified')->group(function () {
        Route::get('/surat-masuk', [AkademikController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [AkademikController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [AkademikController::class, 'showApproval'])->can('akademikCanShowRiwayatPersetujuan', 'approval')->name('show-approval-akademik');
        Route::get('/surat-masuk/show/{surat}', [AkademikController::class, 'showSuratMasuk'])->can('akademikCanShowSuratMasuk', 'surat')->name('show-surat-akademik');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->can('akademikCanShowPreviewSuratMasuk', 'surat')->name('preview-surat-akademik');
        Route::put('/surat-disetujui/{surat}', [AkademikController::class, 'setujuiSurat'])->can('akademikCanApproveSuratMasuk', 'surat')->name('setujui-surat-akademik');
        Route::get('/surat-ditolak/{surat}', [AkademikController::class, 'confirmTolakSurat'])->can('akademikCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat-akademik');
        Route::put('/surat-ditolak/{surat}', [AkademikController::class, 'tolakSurat'])->can('akademikCanDenySuratMasuk', 'surat')->name('tolak-surat-akademik');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('akademikCanPrintSurat', 'surat')->name('print-surat-akademik');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('akademikCanShowLampiranSurat', 'surat')->name('show-file-akademik');
        // });
        Route::get('/', [AkademikController::class, 'dashboard']);
        Route::get('/profile', [AkademikController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [AkademikController::class, 'updateProfile'])->name('update-profile-akademik');
    });
});
