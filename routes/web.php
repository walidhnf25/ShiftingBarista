<?php

use App\Http\Controllers\jamShiftController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Models\User;
use App\Http\Controllers\TipePekerjaanController;
use App\Http\Controllers\JadwalShiftController;
use App\Http\Controllers\AuthController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


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

Route::get('/registersso', function () {
    return view('registersso');
})->name('registersso');

// Code Walid
route::middleware(['guest:user'])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');
     Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');
     Route::post('/authSSO' , [AuthController::class, 'authSSO'])->name('authSSO');
});

Route::middleware(['auth:user', 'checkRole:Staff'])->group(function () {

});

Route::middleware(['auth:user', 'checkRole:Manager'])->group(function () {
    // tipe pekerjaan
    Route::get('/tipepekerjaan', [TipePekerjaanController::class, 'index'])->name('tipepekerjaan');
    Route::post('/tipepekerjaan', [TipePekerjaanController::class, 'store'])->name('tipe_pekerjaan.store');
    Route::get('/editTipePekerjaan', [TipePekerjaanController::class, 'editTipePekerjaan']);
    Route::post('/tipepekerjaan/update/{id}', [TipePekerjaanController::class, 'update'])->name('tipepekerjaan.update');
    Route::post('/tipepekerjaan/delete/{id}', [TipePekerjaanController::class, 'deleteTipePekerjaan'])->name('tipepekerjaan.deleteTipePekerjaan');

    // add pegawai
    Route::get('/addpegawai', [UsersController::class,'index'])->name('addpegawai');
    Route::post('/addpegawai', [UsersController::class,'store'])->name('addpegawai.store');
    Route::delete('/addpegawai/email/{email}', [UsersController::class, 'destroy'])->name('addpegawai.destroy');
    Route::put('/addpegawai/{id}', [UsersController::class, 'update'])->name('addpegawai.update');

    Route::get('/jadwalshift', [JadwalShiftController::class, 'index'])->name('jadwalshift');
    Route::get('/outlet', [JadwalShiftController::class, 'listOutlets'])->name('outlet');
    Route::get('/outlet/jadwalshift/{id}', [JadwalShiftController::class, 'showOutlet'])->name('outlet.jadwalshift');
    Route::post('/outlet/jadwalshift/{id}', [JadwalShiftController::class, 'store'])->name('jadwal_shift.store');
    Route::delete('/jadwalshift/{id}', [JadwalShiftController::class, 'destroy'])->name('jadwal_shift.destroy');
    Route::put('/jadwalshift/{id}', [JadwalShiftController::class, 'update'])->name('jadwalshift.update');

    // jam shift
    Route::get('/jamshift', [jamShiftController::class, 'index'])->name('jamshift');
    Route::post('/jamshift', [jamShiftController::class, 'store'])->name('jam_shift.store');
    Route::get('/editjamshift', [jamShiftController::class, 'editJamShift']);
    Route::post('/jamshift/update/{id}', [jamShiftController::class, 'update'])->name('jamShift.update');
    Route::post('/jamshift/delete/{id}', [jamShiftController::class, 'deleteJamShift'])->name('jamshift.deleteJamShift');

});

Route::middleware(['auth:user'])->group(function () {

    Route::get('/index', function () {
        return view('index');
    })->name('index');
    // ngecegah register ini cok
    // Route::post('/authSSO' , [AuthController::class, 'authSSO'])->name('authSSO');
    Route::get('/proseslogout', [AuthController::class, 'proseslogout'])->name('proseslogout');
});

Route::get('/createrolepermission', function(){
    try {
        Role::create(['name' => 'staff']);
        Permission::create(['name' => 'view-jamshift']);
        echo "Success";
    } catch (\Exception $e) {
        echo "Error";
    }
});

Route::get('/give-user-role', function(){
    try {
        $user = User::findorfail(2);
        $user->assignRole('staff');
        echo "Success";
    } catch (\Exception $th) {
        //throw $th;
        echo "Error";
    }
});

Route::get('/give-user-permission', function(){
    try {
        $role = Role::findorfail(2);
        $role->givePermissionTo('view-jamshift');
        echo "Success";
    } catch (\Exception $th) {
        //throw $th;
        echo "Error";
    }
});
