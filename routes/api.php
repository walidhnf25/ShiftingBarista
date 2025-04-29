<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalShiftController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/jadwalshift', [JadwalShiftController::class, 'getJadwalShiftData']);
Route::get('/users', [JadwalShiftController::class, 'getUserData']);
Route::get('/jamShift', [JadwalShiftController::class, 'getJamShiftData']);
Route::get('/TipePekerjaan', [JadwalShiftController::class, 'getTipePekerjaanData']);