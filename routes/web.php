<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WDController;

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


Route::middleware('guest')->group(function() {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'create']);
    Route::post('/login',[AuthController::class, 'authenticate'])->name('authLogin');
});

Route::middleware('auth')->group(function(){

    Route::get('/logout',[AuthController::class, 'logout'])->name('logout');
    // Route::get('/admin',[AdminController::class,'dashboard'])->middleware('userAccess:1');
    // Route::get('/admin/mahasiswa',[AdminController::class,'mahasiswa'])->middleware('userAccess:1');
    Route::prefix('admin')->group(function() {
        Route::get('/', [AdminController::class, 'dashboard'])->middleware('userAccess:1');
        Route::get('/mahasiswa', [AdminController::class, 'mahasiswa'])->middleware('userAccess:1')->name('mahasiswa');

    });
    Route::get('/mahasiswa',[MahasiswaController::class,'dashboard'])->middleware('userAccess:2');
    Route::get('/staff',[StaffController::class,'dashboard'])->middleware('userAccess:3');
    Route::get('/kaprodi',[KaprodiController::class,'dashboard'])->middleware('userAccess:4');
    Route::get('/wd',[WDController::class,'dashboard'])->middleware('userAccess:5');
});
Route::get('/home', function(){
    return redirect('/admin');
});
Route::get('/login', function(){
    return redirect('/');
});

