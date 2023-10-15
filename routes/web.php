<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

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
    Route::get('/admin',[AdminController::class,'index']);
});
Route::get('/home', function(){
    return redirect('/admin');
});
Route::get('/login', function(){
    return redirect('/');
});

