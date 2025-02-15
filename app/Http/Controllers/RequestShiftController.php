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
use App\Models\PeriodeGaji;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class RequestShiftController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data periode gaji untuk dropdown
        $periode_gaji = PeriodeGaji::all();
        
        // Ambil ID periode yang dipilih dari request
        $id_periode = $request->id_periode;

        // Query dasar: hanya menampilkan jadwal shift dengan status "Waiting" dan memiliki kesediaan
        $jadwal_shift = JadwalShift::where('status', 'Waiting')
            ->whereHas('kesediaan')
            ->with(['tipePekerjaan', 'kesediaan.user', 'jamShift']);

        // Filter berdasarkan periode gaji jika dipilih
        if ($id_periode) {
            $periode = PeriodeGaji::find($id_periode);
            if ($periode) {
                $jadwal_shift->whereBetween('tanggal', [$periode->tgl_mulai, $periode->tgl_akhir]);
            }
        }

        // Ambil daftar jadwal shift yang sudah difilter
        $jadwal_shift = $jadwal_shift->get();

        // Data tambahan untuk tampilan
        $kesediaan = Kesediaan::with(['user'])->get();
        $users = User::all();

        return view('manager.requestshift', compact('jadwal_shift', 'users', 'kesediaan', 'periode_gaji', 'id_periode'));
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
