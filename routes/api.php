<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JadwalShiftController;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TipePekerjaanController;
use App\Http\Controllers\jamShiftController;
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

Route::get('/tipe-pekerjaan/{id?}', [TipePekerjaanController::class, 'getTipePekerjaanData']);
Route::post('/tipe-pekerjaan', [TipePekerjaanController::class, 'createTipePekerjaan']);
Route::put('/tipe-pekerjaan/{id}', [TipePekerjaanController::class, 'updateTipePekerjaan']);
Route::delete('/tipe-pekerjaan/{id}', [TipePekerjaanController::class, 'deleteTipePekerjaanAPI']);

Route::get('jam-shift/{id?}', [jamShiftController::class, 'getJamShift']);
Route::post('jam-shift', [jamShiftController::class, 'createJamShift']);
Route::put('jam-shift/{id}', [jamShiftController::class, 'updateJamShift']);
Route::delete('jam-shift/{id}', [jamShiftController::class, 'deleteJamShiftAPI']);

Route::get('jadwal-shift/{id?}', [JadwalShiftController::class, 'getJadwalShiftData']);
Route::put('jadwal-shift/{id}', [JadwalShiftController::class, 'updateJadwalShift']);

Route::get('users', [AuthController::class, 'getUsers']);
Route::post('auth/LoginWithNumber', [AuthController::class, 'LoginWithNumber']);
Route::post('auth/LoginWithUsername', [AuthController::class, 'LoginWithUsername']);