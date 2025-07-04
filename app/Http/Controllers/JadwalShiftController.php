<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\JamShift;
use App\Models\TipePekerjaan;
use App\Models\PeriodeGaji;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class JadwalShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Eager load TipePekerjaan with JadwalShift
        $jadwal_shift = JadwalShift::with('tipePekerjaan')->get();

        // Retrieve other necessary data
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $User = User::all();
        $apiOutlet = $this->getOutletData();

        // Map outlet IDs to their names
        $outletMapping = [];
        foreach ($apiOutlet as $outlet) {
            $outletMapping[$outlet['id']] = $outlet['outlet_name'];
        }
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        return view('manager.jadwalshift', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'User', 'apiOutlet', 'outletMapping'));
    }

    public function listOutlets(Request $request)
    {
        // Retrieve all necessary data for displaying the list of outlets
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $jadwal_shift = JadwalShift::all();
        $apiOutlet = $this->getOutletData();
        $apiOutlet = collect($apiOutlet);

        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass data to the view
        return view('outlet', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'apiOutlet', 'outletMapping'));
    }

    public function showOutlet(Request $request, $id)
    {
        // Jika ID outlet tidak ditemukan, lempar error 404
        if (!$id) {
            abort(404, 'Outlet ID is required.');
        }

        // Ambil semua data yang diperlukan
        $jamShift = JamShift::where('id_outlet', $id)->get();
        $TipePekerjaan = TipePekerjaan::all();
        $User = User::whereIn('role', ['Staff', 'Manager'])->get();
        $apiOutlet = $this->getOutletData();
        $periode_gaji = PeriodeGaji::all();

        // Cari outlet berdasarkan ID
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);

        // Jika outlet tidak ditemukan, lempar error 404
        if (!$selectedOutlet) {
            abort(404, 'Outlet not found');
        }

        // Ambil ID periode dari request
        $id_periode = $request->id_periode;

        // Default: Ambil semua shift jika tidak ada periode dipilih
        $jadwal_shift = JadwalShift::with('tipePekerjaan', 'jamShift', 'user')
            ->where('id_outlet', $id);

        // Jika ada periode yang dipilih, filter berdasarkan `tgl_mulai` dan `tgl_akhir`
        if ($id_periode) {
            $periode = PeriodeGaji::find($id_periode);
            if ($periode) {
                $jadwal_shift = $jadwal_shift->whereBetween('tanggal', [$periode->tgl_mulai, $periode->tgl_akhir]);
            }
        }

        // Ambil daftar jadwal shift yang sudah difilter
        $jadwal_shift = $jadwal_shift->get();

        // Mapping outlet ID ke nama outlet
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Kirim data ke view
        return view('manager.jadwalshift', compact(
            'jadwal_shift', 'jamShift', 'TipePekerjaan', 'User',
            'selectedOutlet', 'outletMapping', 'apiOutlet', 'periode_gaji', 'id_periode'
        ));
    }

    public function getOutletData()
    {
        // Token API dan URL
        $apiToken = '92|BN2EvdcWabONwrvbSIbFgSZyPoEoFwjsRwse7li6';
        $apiUrl = 'https://pos.lakesidefnb.group/api/outlet'; // Menyesuaikan URL API

        // GuzzleHttp client untuk membuat request
        $client = new Client();

        try {
            // Mengirim request GET ke API
            $response = $client->request('GET', $apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Mengambil body dari response dan mengubah menjadi array
            $responseData = json_decode($response->getBody(), true);

            // Mengembalikan data outlet jika tersedia, atau array kosong jika tidak
            if (isset($responseData['data']) && is_array($responseData['data'])) {
                return $responseData['data'];
            } else {
                return [];
            }
        } catch (\Exception $e) {
            // Logging jika terjadi kesalahan
            Log::error('API Request Error: ' . $e->getMessage());
            return [];
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_jam' => 'required|string',
            'id_tipe_pekerjaan' => 'required|string',
            'id_user' => 'nullable|string',
            'tanggal' => 'required|date',
        ]);

        // Cari jadwal shift berdasarkan ID
        $jadwal_shift = JadwalShift::find($id);

        // Jika jadwal shift tidak ditemukan, berikan response gagal
        if (!$jadwal_shift) {
            return redirect()->route('manager.jadwalshift')->with('error', 'Jadwal Shift tidak ditemukan');
        }

        // Cek apakah id_user berubah
        if ($jadwal_shift->id_user !== $request->id_user) {
            $jadwal_shift->status = 'Approve';
        }

        // Update data jadwal shift
        $jadwal_shift->id_jam = $request->id_jam;
        $jadwal_shift->id_tipe_pekerjaan = $request->id_tipe_pekerjaan;
        $jadwal_shift->id_user = $request->id_user;
        $jadwal_shift->tanggal = $request->tanggal;

        // Simpan perubahan
        $jadwal_shift->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Jadwal Shift berhasil diupdate');
    }

    public function getJadwalShiftData(Request $request, $id = null)
    {
        if ($id) {
            // Jika ada ID, ambil data berdasarkan ID
            $jadwal_shift = JadwalShift::find($id);
            
            // Jika data ditemukan, kembalikan response JSON
            if ($jadwal_shift) {
                return response()->json(['JadwalShift' => $jadwal_shift]);
            } else {
                // Jika data tidak ditemukan, kembalikan response 404
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
        } else {
            // Jika tidak ada ID, ambil semua data JadwalShift
            $jadwal_shift = JadwalShift::all();
            
            // Pastikan nama variabel konsisten, dan kembalikan response JSON
            return response()->json(['jadwal_shift' => $jadwal_shift]);
        }
    }

    public function updateJadwalShift(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_jam' => 'nullable|string',
            'id_tipe_pekerjaan' => 'nullable|string',
            'id_user' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'task' => 'nullable|string',
            'task_status' => 'nullable|string',
        ]);

        // Cari jadwal shift berdasarkan ID
        $jadwal_shift = JadwalShift::find($id);

        // Jika jadwal shift tidak ditemukan, berikan response gagal
        if (!$jadwal_shift) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal Shift tidak ditemukan'
            ], 404);
        }

        // Cek apakah id_user berubah, jika iya, set status menjadi 'Approve'
        if ($jadwal_shift->id_user !== $request->id_user) {
            $jadwal_shift->status = 'Approve';
        }

        // Update data jadwal shift
        $jadwal_shift->id_jam = $request->id_jam;
        $jadwal_shift->id_tipe_pekerjaan = $request->id_tipe_pekerjaan;
        $jadwal_shift->id_user = $request->id_user;
        $jadwal_shift->tanggal = $request->tanggal;
        $jadwal_shift->check_in_time = $request->check_in_time;   // Update waktu check-in
        $jadwal_shift->check_out_time = $request->check_out_time; // Update waktu check-out
        $jadwal_shift->task = $request->task;
        $jadwal_shift->task_status = $request->task_status;

        // Simpan perubahan
        $jadwal_shift->save();

        // Mengembalikan response sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal Shift berhasil diupdate',
            'data' => $jadwal_shift
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function store(Request $request, $id)
    {
        // Validate the form data
        $request->validate([
            'id_jam' => 'required|string',
            'id_tipe_pekerjaan' => 'required|string', 
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        // Initialize start and end dates
        $startDate = new \DateTime($request->tanggal_mulai);
        $endDate = new \DateTime($request->tanggal_akhir);

        // Loop through each date from start to end date
        for ($date = $startDate; $date <= $endDate; $date->modify('+1 day')) {
            // Save new data with looping
            JadwalShift::create([
                'id_jam' => $request->id_jam,
                'id_tipe_pekerjaan' => $request->id_tipe_pekerjaan,
                'tanggal' => $date->format('Y-m-d'), // Assign the current date in loop
                'id_outlet' => $id,
                'id_user' => null,
                'status' => "Waiting",
            ]);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Jadwal Shift berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        // Cari JadwalShift berdasarkan ID
        $JadwalShift = JadwalShift::where('id', $id);

        // Jika user tidak ditemukan, berikan response gagal
        if (!$JadwalShift) {
            return redirect()->route('manager.jadwalshift')->with('error', 'Jadwal Shift tidak ditemukan');
        }

        // Hapus user
        $JadwalShift->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Jadwal Shift berhasil dihapus.');
    }
}
