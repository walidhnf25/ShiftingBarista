<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\JamShift;
use App\Models\Kesediaan;
use App\Models\TipePekerjaan;
use App\Models\RequestShift;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class RequestShiftController extends Controller
{
    public function index(){
        $jadwal_shift = JadwalShift::with('tipePekerjaan')->get();
        $kesediaan = Kesediaan::with('id_user')->get();
        $users = User::with('name')->get();

        // $requestShift = RequestShift::with(['jadwalShift.tipePekerjaan', 'jadwalShift.users'])->get();
        // $jadwalShift = JadwalShift::all();
        // $tipePekerjaan = TipePekerjaan::all();
        // dd($requestShift);
        return view('manager.requestshift', compact('jadwal_shift', 'users'));
    }

}
