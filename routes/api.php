<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sso\LogoutController;
use App\Http\Controllers\Sso\UserInfoController;
use App\Http\Controllers\LegalisirController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
| SSO (OAuth2) identities endpoint untuk aplikasi klien.
| Hanya mahasiswa yang dapat memperoleh token (lihat oauth_client_roles).
*/
Route::middleware(['auth:api', 'scope:profile'])->group(function () {
    Route::get('/sso/userinfo', UserInfoController::class)->name('sso.userinfo');
    Route::post('/sso/logout', LogoutController::class)->name('sso.logout');
});

Route::post('/hitung-harga', [LegalisirController::class, 'hitungHarga']);
