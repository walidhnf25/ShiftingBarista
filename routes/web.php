<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Models\Role;
use App\Models\User;

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

Route::get('/index', function () {
    return view('index');
})->name('index');

Route::get('/buttons', function () {
    return view('buttons');
})->name('buttons');

Route::get('/404', function () {
    return view('404');
})->name('404');

Route::get('/blank', function () {
    return view('blank');
})->name('blank');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/addpegawai', [UsersController::class,'index'])->name('addpegawai');
Route::post('/addpegawai', [UsersController::class,'store'])->name('addpegawai.store');
Route::delete('/addpegawai/{id}', [UsersController::class, 'destroy'])->name('addpegawai.destroy');
Route::put('/addpegawai/{id}', [UsersController::class, 'update'])->name('addpegawai.update');








