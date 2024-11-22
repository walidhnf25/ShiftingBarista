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
    public function index() {
        $jadwal_shift = JadwalShift::where('status', 'Waiting') // Filter jadwal dengan status 'Waiting'
            ->whereHas('kesediaan') // Pastikan hanya jadwal yang memiliki kesediaan
            ->with(['tipePekerjaan', 'kesediaan.user']) // Relasi yang diperlukan
            ->get();
    
        $kesediaan = Kesediaan::with(['user'])->get();
        $users = User::all();
    
        return view('manager.requestshift', compact('jadwal_shift', 'users', 'kesediaan'));
    }    

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'selected_shifts' => 'required|array', // Ensure at least one checkbox is selected
            'selected_user' => 'required|array',  // Ensure user dropdown values are provided
        ]);

        $selectedShifts = $request->input('selected_shifts'); // Array of selected shift IDs
        $selectedUsers = $request->input('selected_user');    // Array of user IDs keyed by shift ID

        // Loop through each selected shift ID
        foreach ($selectedShifts as $shiftId) {
            $userId = $selectedUsers[$shiftId] ?? null;

            if ($userId) {
                // Update the jadwal_shift table
                JadwalShift::where('id', $shiftId)->update([
                    'id_user' => $userId,  // Assign the selected user
                    'status' => 'Approve', // Update status to 'Approve'
                ]);
            }
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Jadwal shift berhasil diupdate.');
    }

}
