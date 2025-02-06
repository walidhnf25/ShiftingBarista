<?php

use App\Http\Controllers\jamShiftController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Models\User;
use App\Http\Controllers\TipePekerjaanController;
use App\Http\Controllers\JadwalShiftController;
use App\Http\Controllers\TukarShiftController;
use App\Http\Controllers\ApplyShiftController;
use App\Http\Controllers\RequestShiftController;
use App\Http\Controllers\WaktuShiftController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CekGajiController;
use App\Http\Controllers\ResetAvailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeriodeGajiController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use App\Models\JadwalShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

Route::get('/register', function () {
    return view('register');
})->name('register');

// Code Walid
route::middleware(['guest:user'])->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');
     Route::post('/proseslogin', [AuthController::class, 'proseslogin'])->name('proseslogin');
     Route::post('/authSSO' , [AuthController::class, 'authSSO'])->name('authSSO');
     Route::post('/registerakun', [AuthController::class, 'registerakun'])->name('registerakun');
});

Route::middleware(['auth:user', 'checkRole:Manager,Staff'])->group(function () {
    Route::get('/waktushift', [WaktuShiftController::class, 'showCalendar'])->name('waktushift');
    Route::get('/user-shift', [DashboardController::class, 'getUserShiftDetails'])->name('userShiftDetails');
    Route::get('/staffdashboard', [DashboardController::class, 'staffdashboard'])->name('staffdashboard');

    Route::get('/applyshift', [ApplyShiftController::class, 'index'])->name('applyshift');
    Route::get('/filter-jadwal-shift', [ApplyShiftController::class, 'filterJadwalShift'])->name('filterJadwalShift');
    Route::get('/store-shift/{id}', [ApplyShiftController::class, 'storeAndGetJadwalShift']);
    Route::post('/kesediaan/store', [ApplyShiftController::class, 'store'])->name('kesediaan.store');
    Route::post('/removeFromCache/{id}', [ApplyShiftController::class, 'removeFromCache'])->name('removeFromCache');

    // Cek Gaji
    Route::get('/cekgaji', [CekGajiController::class, 'IndexStaff'])->name('staffcekgaji');
    Route::get('cekgaji/filter-byDate', [CekGajiController::class, 'filterByDateStaff'])->name('staff.cekgaji.filter');
  

    Route::get('/index', function(){
        $jadwal_shift = JadwalShift::get();
        return view('index', ['jadwal_shift' => $jadwal_shift]);
    });

    Route::get('/putCache', function(){
        $seconds = '10';
        $jadwal_shift = JadwalShift::all();
        Cache::put('jadwal_shift', $jadwal_shift, $seconds);
    });

    Route::get('/getCache', function(){
        $jadwal_shift = Cache::get('jadwal_shift');
        return view('index', ['jadwal_shift' => $jadwal_shift]);
    });

    Route::get('getJadwalshift/{id}', [ApplyShiftController::class, 'getJadwalShift'])->name('getJadwalShift');
    Route::get('/storeAndGetJadwalshift/{id}', [ApplyShiftController::class, 'storeAndGetJadwalShift'])->name('storeAndGetJadwalshift');

    Route::get('getJadwalshift', function () {
        $seconds = 10;
        $jadwal_shifts = [];

        // Ambil semua ID dari database untuk memastikan semua data masuk ke cache
        $ids = JadwalShift::pluck('id'); // Mengambil semua ID dari tabel jadwal_shift

        // Loop setiap ID dan simpan di cache jika belum ada
        foreach ($ids as $id) {
            $jadwal_shifts[] = Cache::remember("jadwal_shift_{$id}", $seconds, function() use ($id) {
                return JadwalShift::find($id); // Menyimpan setiap data berdasarkan ID
            });
        }

        return view('index', ['jadwal_shifts' => $jadwal_shifts]);
    });
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
    Route::delete('/addpegawai/{id}', [UsersController::class, 'destroy'])->name('addpegawai.destroy');
    Route::put('/addpegawai/{id}', [UsersController::class, 'update'])->name('addpegawai.update');

    //jadwal shift
    Route::get('/jadwalshift', [JadwalShiftController::class, 'index'])->name('jadwalshift');
    Route::get('/tukarshift', [TukarShiftController::class, 'showOutlet'])->name('tukarshift');
    Route::post('/jadwal-shift/update-user', [TukarShiftController::class, 'update'])->name('jadwalshift.update-user');
    Route::get('/filter-jadwal-shifts', [TukarShiftController::class, 'filter'])->name('filterJadwalShifts');
    Route::get('/outlet', [JadwalShiftController::class, 'listOutlets'])->name('outlet');
    Route::get('/outlet/jadwalshift/{id}', [JadwalShiftController::class, 'showOutlet'])->name('outlet.jadwalshift');
    Route::post('/outlet/jadwalshift/{id}', [JadwalShiftController::class, 'store'])->name('jadwal_shift.store');
    Route::delete('/jadwalshift/{id}', [JadwalShiftController::class, 'destroy'])->name('jadwal_shift.destroy');
    Route::put('/jadwalshift/{id}', [JadwalShiftController::class, 'update'])->name('jadwalshift.update');
    
    // Cek Gaji
    Route::get('/cekgajimanagerview', function () {
        return view('manager.cekgaji');
    })->name('managercekgaji');
    Route::get('/cekgajioutlet', [CekGajiController::class, 'listOutlets'])->name('cekgajioutlet');
    Route::get('/cekgajioutlet/cekgaji/{id}', [CekGajiController::class, 'IndexManager'])->name('manager.cekgaji');
    // Filter Cek Gaji
    Route::get('/cekgajioutlet/filter-byDate/{id}', [CekGajiController::class, 'filterByDateManager'])->name('manager.cekgaji.filter');
    // Search Query By Name
    Route::get('cekgajioutlet/search/{id}', [CekGajiController::class, 'searchByNameManager'])->name('manager.cekgaji.search');
    Route::get('/cekgajioutlet/cetak/{id}', [CekGajiController::class, 'cetakPDF'])->name('manager.cekgaji.cetakpdf');

    // jam shift
    Route::get('/jamshift', [jamShiftController::class, 'index'])->name('jamshift');
    Route::post('/jamshift', [jamShiftController::class, 'store'])->name('jam_shift.store');
    Route::get('/editjamshift', [jamShiftController::class, 'editJamShift']);
    Route::post('/jamshift/update/{id}', [jamShiftController::class, 'update'])->name('jamShift.update');
    Route::post('/jamshift/delete/{id}', [jamShiftController::class, 'deleteJamShift'])->name('jamshift.deleteJamShift');

    // request shift
    Route::get('/requestshift', [RequestShiftController::class, 'index'])->name('requestshift');
    Route::post('/requestshift', [RequestShiftController::class, 'store'])->name('requestshift.store');

    // Reset Availability User
    Route::get('/resetavail', [ResetAvailController::class, 'index'])->name('resetavail');
    Route::post('/resetavail', [ResetAvailController::class, 'store'])->name('resetavail.store');


    Route::get('/managerdashboard', function () {
        return view('manager.dashboard');
    })->name('managerdashboard');

    Route::get('/managerdashboard', [DashboardController::class, 'showCalendar'])->name('managerdashboard');

    // Periode Gaji
    Route::get('/periodegaji', [PeriodeGajiController::class, 'index'])->name('periodegaji.index');
    Route::post('/periodegaji', [PeriodeGajiController::class, 'store'])->name('periodegaji.store');
    Route::get('/editPeriodeGaji', [PeriodeGajiController::class, 'editPeriodeGaji']);
    Route::post('/periodegaji/update/{id}', [PeriodeGajiController::class, 'update'])->name('periodegaji.update');
    Route::post('/periodegaji/delete/{id}', [PeriodeGajiController::class, 'delete'])->name('periodegaji.delete');
    
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
