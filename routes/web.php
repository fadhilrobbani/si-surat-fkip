<?php

use App\Models\User;
use App\Models\Akademik;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use App\Events\NotificationCreated;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WDController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\WD2Controller;
use App\Http\Controllers\WD3Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DekanController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\KaprodiController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\AkademikController;
use App\Http\Controllers\StaffWD1Controller;
use App\Http\Controllers\StaffWD2Controller;
use App\Http\Controllers\StaffWD3Controller;
use App\Http\Controllers\LegalisirController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\StaffDekanController;
use App\Http\Controllers\StaffNilaiController;
use App\Http\Controllers\JenisLegalisirController;
use App\Http\Controllers\AkademikFakultasController;
use App\Http\Controllers\PengirimLegalisirController;
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


Route::get('/home', [AuthController::class, 'home']);
Route::get('/login', function () {
    return redirect('/');
});

Route::get('/surat-terverifikasi/{surat}', [SuratController::class, 'previewSuratQR'])->middleware('signed')->name('preview-surat-qr');
Route::get('/surat-terverifikasi/{surat}/cetak', [PDFController::class, 'previewSurat'])->middleware('signed')->name('cetak-surat-qr');
Route::get('/register', [AuthController::class, 'create']);
Route::post('/register/new', [AuthController::class, 'store'])->name('register-user');

Route::get('/email/verify', function () {
    if (auth()->user()->email_verified_at == null) {

        return view('auth.verify-email');
    } else {
        return redirect('/home');
    }
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
// Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-mahasiswa');
Route::get('/report-bug', [EmailController::class, 'reportBug'])->name('report-bug');


Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('admin')->middleware(['userAccess:1'])->group(function () {
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-admin');
    });
    Route::prefix('mahasiswa')->middleware(['userAccess:2'])->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard']);
        Route::middleware('verified')->group(function () {

            Route::get('/pengajuan-surat', [MahasiswaController::class, 'pengajuanSurat']);
            Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('show-form-surat');
            Route::get('/pengajuan-legalisir', [MahasiswaController::class, 'pengajuanLegalisir']);
            Route::get('/pengajuan-legalisir/{jenisSurat:slug}', [LegalisirController::class, 'create'])->name('show-form-legalisir');
            Route::post('/pengajuan-legalisir', [JenisLegalisirController::class, 'redirectToFormLegalisir'])->name('redirect-form-legalisir');
            // Route::post('/pengajuan-surat/store/6', [SuratController::class, 'storeSuratKeteranganAlumni'])->name('store-surat-alumni');
            // Route::post('/pengajuan-surat/store/8', [SuratController::class, 'storeSuratKeteranganLulus'])->name('store-surat-lulus');
            Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'store'])->name('store-surat');
            Route::post('/pengajuan-legalisir/store/{jenisSurat:slug}', [LegalisirController::class, 'store'])->name('store-pengajuan-legalisir');
            Route::put('/pengajuan-legalisir/konfirmasi-pembayaran/{surat}', [LegalisirController::class, 'konfirmasiPembayaran'])->name('konfirmasi-pembayaran-legalisir');
            Route::put('/pengajuan-legalisir/konfirmasi-selesai/{surat}', [LegalisirController::class, 'konfirmasiSelesai'])->name('konfirmasi-selesai-legalisir');
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
        Route::get('/profile/reset-password', [MahasiswaController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [MahasiswaController::class, 'resetPassword'])->name('reset-password-mahasiswa');
    });
    Route::prefix('staff')->middleware(['userAccess:3'])->group(function () {
        Route::get('/pengajuan-surat', [StaffController::class, 'pengajuanSurat'])->name('staff-pengajuan-surat');
        Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('staff-redirect-form-surat');
        Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('staff-show-form-surat');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByStaff'])->name('staff-store-surat');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}/berita-acara-nilai', [SuratController::class, 'storeBeritaAcaraNilaiByStaff'])->name('staff-store-berita-acara-nilai');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-tugas', [SuratController::class, 'storeSuratTugasByStaff'])->name('staff-store-surat-tugas');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-tugas-kelompok', [SuratController::class, 'storeSuratTugasKelompokByStaff'])->name('staff-store-surat-tugas-kelompok');
        Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->can('staffCanCancelSurat', 'surat')->name('staff-destroy-surat');
        Route::get('/riwayat-pengajuan-surat', [StaffController::class, 'riwayatPengajuanSurat'])->name('staff-riwayat-pengajuan-surat');
        Route::get('/riwayat-pengajuan-surat/show/{surat}', [StaffController::class, 'showDetailPengajuanSuratByStaff'])->can('staffCanViewShowDetailPengajuanSuratByStaff', 'surat')->name('show-detail-pengajuan-surat-staff');

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
        Route::get('/profile/reset-password', [StaffController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [StaffController::class, 'resetPassword'])->name('reset-password-staff');
    });
    Route::prefix('kaprodi')->middleware(['userAccess:4'])->group(function () {
        Route::get('/surat-masuk', [KaprodiController::class, 'suratMasuk']);
        Route::get('/surat-masuk/show/{surat}', [KaprodiController::class, 'showSuratMasuk'])->can('kaprodiCanShowSuratMasuk', 'surat')->name('show-surat-kaprodi');
        Route::get('/riwayat-persetujuan', [KaprodiController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [KaprodiController::class, 'showApproval'])->can('kaprodiCanShowRiwayatPersetujuan', 'approval')->name('show-approval-kaprodi');
        Route::put('/surat-disetujui/{surat}', [KaprodiController::class, 'setujuiSurat'])->can('kaprodiCanApproveSuratMasuk', 'surat')->name('setujui-surat-kaprodi');
        Route::put('/surat-staff-disetujui/{surat}', [KaprodiController::class, 'setujuiSuratStaff'])->name('setujui-surat-from-staff-kaprodi');
        Route::get('/surat-ditolak/{surat}', [KaprodiController::class, 'confirmTolakSurat'])->can('kaprodiCanShowDenySuratMasuk', 'surat')->name('confirm-tolak-surat-kaprodi');
        Route::put('/surat-ditolak/{surat}', [KaprodiController::class, 'tolakSurat'])->can('kaprodiCanDenySuratMasuk', 'surat')->name('tolak-surat-kaprodi');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->can('kaprodiCanPrintSurat', 'surat')->name('print-surat-kaprodi');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->can('kaprodiCanShowLampiranSurat', 'surat')->name('show-file-kaprodi');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->can('kaprodiCanShowPreviewSuratMasuk', 'surat')->name('preview-surat-kaprodi');

        Route::get('/', [KaprodiController::class, 'dashboard']);
        Route::get('/profile', [KaprodiController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [KaprodiController::class, 'updateProfile'])->name('update-profile-kaprodi');
        Route::get('/profile/reset-password', [KaprodiController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [KaprodiController::class, 'resetPassword'])->name('reset-password-kaprodi');
    });
    Route::prefix('dekan')->middleware(['userAccess:8'])->group(function () {
        Route::get('/surat-masuk', [DekanController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [DekanController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [DekanController::class, 'showApproval'])->can('wdCanShowRiwayatPersetujuan', 'approval')->name('show-approval-dekan');
        Route::get('/surat-masuk/show/{surat}', [DekanController::class, 'showSuratMasuk'])->name('show-surat-dekan');
        Route::put('/surat-disetujui/{surat}', [DekanController::class, 'setujuiSurat'])->name('setujui-surat-dekan');
        Route::put('/surat-staff-disetujui/{surat}', [DekanController::class, 'setujuiSuratStaff'])->name('setujui-surat-staff-dekan');
        Route::put('/surat-staff-dekan-disetujui/{surat}', [DekanController::class, 'setujuiSuratStaffDekan'])->name('setujui-surat-staff-dekan-dekan');
        Route::get('/surat-ditolak/{surat}', [DekanController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-dekan');
        Route::put('/surat-ditolak/{surat}', [DekanController::class, 'tolakSurat'])->name('tolak-surat-dekan');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-dekan');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-dekan');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-dekan');
        Route::get('/', [DekanController::class, 'dashboard']);
        Route::get('/profile', [DekanController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [DekanController::class, 'updateProfile'])->name('update-profile-dekan');
        Route::get('/profile/reset-password', [DekanController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [DekanController::class, 'resetPassword'])->name('reset-password-dekan');
    });
    Route::prefix('wd')->middleware(['userAccess:5'])->group(function () {
        Route::get('/surat-masuk', [WDController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [WDController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [WDController::class, 'showApproval'])->can('wdCanShowRiwayatPersetujuan', 'approval')->name('show-approval-wd');
        Route::get('/surat-masuk/show/{surat}', [WDController::class, 'showSuratMasuk'])->name('show-surat-wd');
        Route::put('/surat-disetujui/{surat}', [WDController::class, 'setujuiSurat'])->name('setujui-surat-wd');
        Route::put('/surat-staff-disetujui/{surat}', [WDController::class, 'setujuiSuratStaff'])->name('setujui-surat-from-staff-wd');
        Route::put('/surat-staff-dekan-disetujui/{surat}', [WDController::class, 'setujuiSuratStaffDekan'])->name('setujui-surat-staff-dekan-wd');

        Route::get('/surat-ditolak/{surat}', [WDController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-wd');
        Route::put('/surat-ditolak/{surat}', [WDController::class, 'tolakSurat'])->name('tolak-surat-wd');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-wd');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-wd');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-wd');
        Route::get('/', [WDController::class, 'dashboard']);
        Route::get('/profile', [WDController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [WDController::class, 'updateProfile'])->name('update-profile-wd');
        Route::get('/profile/reset-password', [WDController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [WDController::class, 'resetPassword'])->name('reset-password-wd');
    });
    Route::prefix('wd2')->middleware(['userAccess:9'])->group(function () {
        Route::get('/surat-masuk', [WD2Controller::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [WD2Controller::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [WD2Controller::class, 'showApproval'])->can('wdCanShowRiwayatPersetujuan', 'approval')->name('show-approval-wd2');
        Route::get('/surat-masuk/show/{surat}', [WD2Controller::class, 'showSuratMasuk'])->name('show-surat-wd2');
        Route::put('/surat-disetujui/{surat}', [WD2Controller::class, 'setujuiSurat'])->name('setujui-surat-wd2');
        Route::put('/surat-staff-disetujui/{surat}', [WD2Controller::class, 'setujuiSuratStaff'])->name('setujui-surat-from-staff-wd2');
        Route::put('/surat-staff-dekan-disetujui/{surat}', [WD2Controller::class, 'setujuiSuratStaffDekan'])->name('setujui-surat-staff-dekan-wd2');

        Route::get('/surat-ditolak/{surat}', [WD2Controller::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-wd2');
        Route::put('/surat-ditolak/{surat}', [WD2Controller::class, 'tolakSurat'])->name('tolak-surat-wd2');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-wd2');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-wd2');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-wd2');
        Route::get('/', [WD2Controller::class, 'dashboard']);
        Route::get('/profile', [WD2Controller::class, 'profilePage']);
        Route::put('/profile/update/{user}', [WD2Controller::class, 'updateProfile'])->name('update-profile-wd2');
        Route::get('/profile/reset-password', [WD2Controller::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [WD2Controller::class, 'resetPassword'])->name('reset-password-wd2');
    });
    Route::prefix('wd3')->middleware(['userAccess:10'])->group(function () {
        Route::get('/surat-masuk', [WD3Controller::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [WD3Controller::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [WD3Controller::class, 'showApproval'])->can('wdCanShowRiwayatPersetujuan', 'approval')->name('show-approval-wd3');
        Route::get('/surat-masuk/show/{surat}', [WD3Controller::class, 'showSuratMasuk'])->name('show-surat-wd3');
        Route::put('/surat-disetujui/{surat}', [WD3Controller::class, 'setujuiSurat'])->name('setujui-surat-wd3');
        Route::put('/surat-staff-disetujui/{surat}', [WD3Controller::class, 'setujuiSuratStaff'])->name('setujui-surat-from-staff-wd3');
        Route::put('/surat-staff-dekan-disetujui/{surat}', [WD3Controller::class, 'setujuiSuratStaffDekan'])->name('setujui-surat-staff-dekan-wd3');

        Route::get('/surat-ditolak/{surat}', [WD3Controller::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-wd3');
        Route::put('/surat-ditolak/{surat}', [WD3Controller::class, 'tolakSurat'])->name('tolak-surat-wd3');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-wd3');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-wd3');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-wd3');
        Route::get('/', [WD3Controller::class, 'dashboard']);
        Route::get('/profile', [WD3Controller::class, 'profilePage']);
        Route::put('/profile/update/{user}', [WD3Controller::class, 'updateProfile'])->name('update-profile-wd3');
        Route::get('/profile/reset-password', [WD3Controller::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [WD3Controller::class, 'resetPassword'])->name('reset-password-wd3');
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
        Route::get('/profile/reset-password', [AkademikController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [AkademikController::class, 'resetPassword'])->name('reset-password-akademik');
    });
    Route::prefix('akademik-fakultas')->middleware(['userAccess:16'])->group(function () {
        // Route::middleware('verified')->group(function () {
        Route::get('/surat-masuk', [AkademikFakultasController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [AkademikFakultasController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [AkademikFakultasController::class, 'showApproval'])->name('show-approval-akademik-fakultas');
        Route::get('/surat-masuk/show/{surat}', [AkademikFakultasController::class, 'showSuratMasuk'])->name('show-surat-akademik-fakultas');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-akademik-fakultas');
        Route::put('/surat-disetujui/{surat}', [AkademikFakultasController::class, 'setujuiSurat'])->name('setujui-surat-akademik-fakultas');
        Route::get('/surat-ditolak/{surat}', [AkademikFakultasController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-akademik-fakultas');
        Route::put('/surat-ditolak/{surat}', [AkademikFakultasController::class, 'tolakSurat'])->name('tolak-surat-akademik-fakultas');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-akademik-fakultas');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-akademik-fakultas');
        // });
        Route::get('/', [AkademikFakultasController::class, 'dashboard']);
        Route::get('/profile', [AkademikFakultasController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [AkademikFakultasController::class, 'updateProfile'])->name('update-profile-akademik-fakultas');
        Route::get('/profile/reset-password', [AkademikFakultasController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [AkademikFakultasController::class, 'resetPassword'])->name('reset-password-akademik-fakultas');
    });

    Route::prefix('staff-nilai')->middleware(['userAccess:7'])->group(function () {
        // Route::middleware('verified')->group(function () {
        Route::get('/surat-masuk', [StaffNilaiController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [StaffNilaiController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffNilaiController::class, 'showApproval'])->name('show-approval-staff-nilai');
        Route::get('/surat-masuk/show/{surat}', [StaffNilaiController::class, 'showSuratMasuk'])->name('show-surat-staff-nilai');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-staff-nilai');
        Route::put('/surat-disetujui/{surat}', [StaffNilaiController::class, 'setujuiSurat'])->name('setujui-surat-staff-nilai');
        Route::get('/surat-ditolak/{surat}', [StaffNilaiController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-staff-nilai');
        Route::put('/surat-ditolak/{surat}', [StaffNilaiController::class, 'tolakSurat'])->name('tolak-surat-staff-nilai');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-staff-nilai');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-staff-nilai');
        // });
        Route::get('/', [StaffNilaiController::class, 'dashboard']);
        Route::get('/profile', [StaffNilaiController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [StaffNilaiController::class, 'updateProfile'])->name('update-profile-staff-nilai');
        Route::get('/profile/reset-password', [StaffNilaiController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [StaffNilaiController::class, 'resetPassword'])->name('reset-password-staff-nilai');
    });
    Route::prefix('staff-wd1')->middleware(['userAccess:11'])->group(function () {
        // Route::middleware('verified')->group(function () {
        Route::get('/surat-masuk', [StaffWD1Controller::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [StaffWD1Controller::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffWD1Controller::class, 'showApproval'])->name('show-approval-staff-wd1');
        Route::get('/surat-masuk/show/{surat}', [StaffWD1Controller::class, 'showSuratMasuk'])->name('show-surat-staff-wd1');
        Route::get('/surat-masuk/edit/{surat}', [SuratController::class, 'edit'])->name('edit-surat-staff-wd1');
        Route::put('/surat-masuk/edit/{surat}', [SuratController::class, 'update'])->name('update-surat-staff-wd1');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-staff-wd1');
        Route::put('/surat-disetujui/{surat}', [StaffWD1Controller::class, 'setujuiSurat'])->name('setujui-surat-staff-wd1');
        Route::get('/surat-ditolak/{surat}', [StaffWD1Controller::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-staff-wd1');
        Route::put('/surat-ditolak/{surat}', [StaffWD1Controller::class, 'tolakSurat'])->name('tolak-surat-staff-wd1');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-staff-wd1');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-staff-wd1');
        // });
        Route::get('/', [StaffWD1Controller::class, 'dashboard']);
        Route::get('/profile', [StaffWD1Controller::class, 'profilePage']);
        Route::put('/profile/update/{user}', [StaffWD1Controller::class, 'updateProfile'])->name('update-profile-staff-wd1');
        Route::get('/profile/reset-password', [StaffWD1Controller::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [StaffWD1Controller::class, 'resetPassword'])->name('reset-password-staff-wd1');
    });
    Route::prefix('staff-wd2')->middleware(['userAccess:12'])->group(function () {
        // Route::middleware('verified')->group(function () {
        Route::get('/surat-masuk', [StaffWD2Controller::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [StaffWD2Controller::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffWD2Controller::class, 'showApproval'])->name('show-approval-staff-wd2');
        Route::get('/surat-masuk/show/{surat}', [StaffWD2Controller::class, 'showSuratMasuk'])->name('show-surat-staff-wd2');
        Route::get('/surat-masuk/edit/{surat}', [SuratController::class, 'edit'])->name('edit-surat-staff-wd2');
        Route::put('/surat-masuk/edit/{surat}', [SuratController::class, 'update'])->name('update-surat-staff-wd2');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-staff-wd2');
        Route::put('/surat-disetujui/{surat}', [StaffWD2Controller::class, 'setujuiSurat'])->name('setujui-surat-staff-wd2');
        Route::get('/surat-ditolak/{surat}', [StaffWD2Controller::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-staff-wd2');
        Route::put('/surat-ditolak/{surat}', [StaffWD2Controller::class, 'tolakSurat'])->name('tolak-surat-staff-wd2');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-staff-wd2');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-staff-wd2');
        // });
        Route::get('/', [StaffWD2Controller::class, 'dashboard']);
        Route::get('/profile', [StaffWD2Controller::class, 'profilePage']);
        Route::put('/profile/update/{user}', [StaffWD2Controller::class, 'updateProfile'])->name('update-profile-staff-wd2');
        Route::get('/profile/reset-password', [StaffWD2Controller::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [StaffWD2Controller::class, 'resetPassword'])->name('reset-password-staff-wd2');
    });
    Route::prefix('staff-wd3')->middleware(['userAccess:13'])->group(function () {
        // Route::middleware('verified')->group(function () {
        Route::get('/surat-masuk', [StaffWD3Controller::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [StaffWD3Controller::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffWD3Controller::class, 'showApproval'])->name('show-approval-staff-wd3');
        Route::get('/surat-masuk/show/{surat}', [StaffWD3Controller::class, 'showSuratMasuk'])->name('show-surat-staff-wd3');
        Route::get('/surat-masuk/edit/{surat}', [SuratController::class, 'edit'])->name('edit-surat-staff-wd3');
        Route::put('/surat-masuk/edit/{surat}', [SuratController::class, 'update'])->name('update-surat-staff-wd3');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-staff-wd3');
        Route::put('/surat-disetujui/{surat}', [StaffWD3Controller::class, 'setujuiSurat'])->name('setujui-surat-staff-wd3');
        Route::get('/surat-ditolak/{surat}', [StaffWD3Controller::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-staff-wd3');
        Route::put('/surat-ditolak/{surat}', [StaffWD3Controller::class, 'tolakSurat'])->name('tolak-surat-staff-wd3');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-staff-wd3');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-staff-wd3');
        // });
        Route::get('/', [StaffWD3Controller::class, 'dashboard']);
        Route::get('/profile', [StaffWD3Controller::class, 'profilePage']);
        Route::put('/profile/update/{user}', [StaffWD3Controller::class, 'updateProfile'])->name('update-profile-staff-wd3');
        Route::get('/profile/reset-password', [StaffWD3Controller::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [StaffWD3Controller::class, 'resetPassword'])->name('reset-password-staff-wd3');
    });

    Route::prefix('staff-dekan')->middleware(['userAccess:14'])->group(function () {

        Route::get('/pengajuan-surat', [StaffDekanController::class, 'pengajuanSurat'])->name('staff-dekan-pengajuan-surat');
        Route::post('/pengajuan-surat', [JenisSuratController::class, 'redirectToFormSurat'])->name('staff-dekan-redirect-form-surat');
        Route::get('/pengajuan-surat/{jenisSurat:slug}', [SuratController::class, 'create'])->name('staff-dekan-show-form-surat');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}', [SuratController::class, 'storeByStaffDekan'])->name('staff-dekan-store-surat');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-tugas', [SuratController::class, 'storeSuratTugasByStaffDekan'])->name('staff-dekan-store-surat-tugas');
        Route::post('/pengajuan-surat/store/{jenisSurat:slug}/surat-tugas-kelompok', [SuratController::class, 'storeSuratTugasKelompokByStaffDekan'])->name('staff-dekan-store-surat-tugas-kelompok');
        // Route::middleware('verified')->group(function () {
        Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->can('staffDekanCanCancelSurat', 'surat')->name('staff-destroy-surat');
        Route::get('/riwayat-pengajuan-surat', [StaffDekanController::class, 'riwayatPengajuanSurat'])->name('staff-dekan-riwayat-pengajuan-surat');
        Route::get('/riwayat-pengajuan-surat/show/{surat}', [StaffDekanController::class, 'showDetailPengajuanSuratByStaffDekan'])->can('staffDekanCanViewShowDetailPengajuanSuratByStaff', 'surat')->name('show-detail-pengajuan-surat-staff-dekan');
        Route::delete('/pengajuan-surat/destroy/{surat}', [SuratController::class, 'destroy'])->can('staffDekanCanCancelSurat', 'surat')->name('staff-dekan-destroy-surat');

        Route::get('/surat-masuk', [StaffDekanController::class, 'suratMasuk']);
        Route::get('/riwayat-persetujuan', [StaffDekanController::class, 'riwayatPersetujuan']);
        Route::get('/riwayat-persetujuan/show/{approval}', [StaffDekanController::class, 'showApproval'])->name('show-approval-staff-dekan');
        Route::get('/surat-masuk/show/{surat}', [StaffDekanController::class, 'showSuratMasuk'])->name('show-surat-staff-dekan');
        Route::get('/surat-masuk/edit/{surat}', [SuratController::class, 'edit'])->name('edit-surat-staff-dekan');
        Route::put('/surat-masuk/edit/{surat}', [SuratController::class, 'update'])->name('update-surat-staff-dekan');
        Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-staff-dekan');
        Route::put('/surat-disetujui/{surat}', [StaffDekanController::class, 'setujuiSurat'])->name('setujui-surat-staff-staff-dekan');
        Route::get('/surat-ditolak/{surat}', [StaffDekanController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-staff-dekan');
        Route::put('/surat-ditolak/{surat}', [StaffDekanController::class, 'tolakSurat'])->name('tolak-surat-staff-dekan');
        Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-staff-dekan');
        Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-staff-dekan');
        // });
        Route::get('/', [StaffDekanController::class, 'dashboard']);
        Route::get('/profile', [StaffDekanController::class, 'profilePage']);
        Route::put('/profile/update/{user}', [StaffDekanController::class, 'updateProfile'])->name('update-profile-staff-dekan');
        Route::get('/profile/reset-password', [StaffDekanController::class, 'resetPasswordPage']);
        Route::put('/profile/reset-password/{user}', [StaffDekanController::class, 'resetPassword'])->name('reset-password-staff-dekan');
    });

    // Route::prefix('pengirim-legalisir')->middleware(['userAccess:15'])->group(function () {
    //     // Route::middleware('verified')->group(function () {
    //     Route::get('/pengajuan-terbaru', [PengirimLegalisirController::class, 'suratMasuk']);
    //     Route::get('/riwayat-persetujuan', [PengirimLegalisirController::class, 'riwayatPersetujuan']);
    //     Route::get('/riwayat-persetujuan/show/{approval}', [PengirimLegalisirController::class, 'showApproval'])->name('show-approval-pengirim-legalisir');
    //     Route::get('/surat-masuk/show/{surat}', [PengirimLegalisirController::class, 'showSuratMasuk'])->name('show-surat-pengirim-legalisir');
    //     Route::get('/surat-masuk/edit/{surat}', [SuratController::class, 'edit'])->name('edit-surat-pengirim-legalisir');
    //     Route::put('/surat-masuk/edit/{surat}', [SuratController::class, 'update'])->name('update-surat-pengirim-legalisir');
    //     Route::get('/preview-surat/{surat}', [PDFController::class, 'previewSurat'])->name('preview-surat-pengirim-legalisir');
    //     Route::put('/surat-disetujui/{surat}', [PengirimLegalisirController::class, 'setujuiSurat'])->name('setujui-surat-pengirim-legalisir');
    //     Route::get('/surat-ditolak/{surat}', [PengirimLegalisirController::class, 'confirmTolakSurat'])->name('confirm-tolak-surat-pengirim-legalisir');
    //     Route::put('/surat-ditolak/{surat}', [PengirimLegalisirController::class, 'tolakSurat'])->name('tolak-surat-pengirim-legalisir');
    //     Route::get('/print-surat/{surat}', [PDFController::class, 'printSurat'])->name('print-surat-pengirim-legalisir');
    //     Route::get('/show-file/{surat}/{filename}', [FileController::class, 'show'])->name('show-file-pengirim-legalisir');
    //     // });
    //     Route::get('/', [PengirimLegalisirController::class, 'dashboard']);
    //     Route::get('/profile', [PengirimLegalisirController::class, 'profilePage']);
    //     Route::put('/profile/update/{user}', [PengirimLegalisirController::class, 'updateProfile'])->name('update-profile-pengirim-legalisir');
    //     Route::get('/profile/reset-password', [PengirimLegalisirController::class, 'resetPasswordPage']);
    //     Route::put('/profile/reset-password/{user}', [PengirimLegalisirController::class, 'resetPassword'])->name('reset-password-pengirim-legalisir');
    // });
});


Route::get('/storage/{user}/files/{filename?}/{mimeType}/{extension}', function ($user, $filename, $mimeType, $extension) {
    // Logika untuk memeriksa izin pengguna atau status login
    if (auth()->check()) {
        if (!request()->hasValidSignature()) {
            return abort(401);
        }
        $userId = request()->user;
        $authUser = auth()->user();
        if (!($authUser && $authUser->id == $userId)) {
            return abort(403);
        }
        // Jika pengguna login, izinkan akses
        // dd('hehe boi');
        // dd($mimeType);
        $file = public_path('storage/lampiran/' . $filename . '.' . $extension);
        // $url = url('/storage/lampiran/' . $filename . '.' . $extension);

        // return redirect($url);
        // return response()->file(public_path($file, ['Content-Type' => str_replace('-', '/', $mimeType)]));
        return response()->file($file, ['Content-Type' => str_replace('-', '/', $mimeType)]);

        // $tempFile = tempnam(sys_get_temp_dir(), $filename . '.' . $extension);
        // copy($url, $tempFile);

        // return response()->download($tempFile, $filename . '.' . $extension);
    } else {
        // Jika pengguna belum login, tolak akses
        abort(403, 'Unauthorized access');
    }
})->where(['filename' => '.*'])->name('show-file');
