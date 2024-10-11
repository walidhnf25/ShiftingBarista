<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\TipePekerjaanController;
use App\Http\Controllers\AuthController;


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
    return view('welcome');
});

Route::get('/', function () {
    return view('index');
});

Route::get('/tipepekerjaan', function () {
    return view('tipepekerjaan');
})->name('tipepekerjaan');



Route::get('/buttons', function () {
    return view('buttons');
})->name('buttons');

Route::get('/404', function () {
    return view('404');
})->name('404');

Route::get('/blank', function () {
    return view('blank');
})->name('blank');

route::middleware(['guest:user'])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');
     Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');
});

route::middleware(['auth:user'])->group(function () {
    Route::get('/index', function () {
        return view('index');
    })->name('index');

    Route::get('/addpegawai', [UsersController::class,'index'])->name('addpegawai');
    Route::get('/tipepekerjaan', [TipePekerjaanController::class, 'index'])->name('tipepekerjaan');
});

Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('proseslogout');
Route::post('/addpegawai', [UsersController::class,'store'])->name('addpegawai.store');
Route::delete('/addpegawai/{id}', [UsersController::class, 'destroy'])->name('addpegawai.destroy');
Route::put('/addpegawai/{id}', [UsersController::class, 'update'])->name('addpegawai.update');

Route::post('/tipepekerjaan', [TipePekerjaanController::class, 'store'])->name('tipe_pekerjaan.store');
Route::get('/editTipePekerjaan', [TipePekerjaanController::class, 'editTipePekerjaan']);
Route::post('/tipepekerjaan/update/{id}', [TipePekerjaanController::class, 'update'])->name('tipepekerjaan.update');
Route::post('/tipepekerjaan/delete/{id}', [TipePekerjaanController::class, 'deleteTipePekerjaan'])->name('tipepekerjaan.deleteTipePekerjaan');

