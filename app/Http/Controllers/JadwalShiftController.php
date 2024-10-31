<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\JamShift;
use App\Models\TipePekerjaan;
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

        // Mengambil semua data dari TipePekerjaan
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $jadwal_shift = JadwalShift::all();
        $apiOutlet = $this->getOutletData();
        // Buat mapping ID outlet ke nama outlet
        $outletMapping = [];
        foreach ($apiOutlet as $outlet) {
            $outletMapping[$outlet['id']] = $outlet['outlet_name'];
        }
        // Meneruskan data ke tampilan
        return view('jadwalshift', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'apiOutlet', 'outletMapping'));
    }

    public function listOutlets(Request $request)
    {
        // Retrieve all necessary data for displaying the list of outlets
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $jadwal_shift = JadwalShift::all();
        $apiOutlet = $this->getOutletData();

        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass data to the view
        return view('outlet', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'apiOutlet', 'outletMapping'));
    }

    public function showOutlet(Request $request, $id)
    {
        // Retrieve all necessary data
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $jadwal_shift = JadwalShift::all();
        $apiOutlet = $this->getOutletData();

        // Find the specific outlet by ID
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);

        // Check if the outlet exists
        if (!$selectedOutlet) {
            abort(404, 'Outlet not found');
        }

        $jadwal_shift = JadwalShift::where('id_outlet', $id)->get();

        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass all data to the view, including apiOutlet
        return view('jadwalshift', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'selectedOutlet', 'outletMapping', 'apiOutlet'));
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
            'jam_kerja' => 'required|string',
            'tipe_pekerjaan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        // Cari jadwal shift berdasarkan ID
        $jadwal_shift = JadwalShift::find($id);

        // Jika jadwal shift tidak ditemukan, berikan response gagal
        if (!$jadwal_shift) {
            return redirect()->route('jadwalshift')->with('error', 'Jadwal Shift tidak ditemukan');
        }

        // Update data jadwal shift
        $jadwal_shift->jam_kerja = $request->jam_kerja;
        $jadwal_shift->tipe_pekerjaan = $request->tipe_pekerjaan;
        $jadwal_shift->tanggal_mulai = $request->tanggal_mulai;
        $jadwal_shift->tanggal_akhir = $request->tanggal_akhir;

        // Simpan perubahan
        $jadwal_shift->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Jadwal Shift berhasil diupdate');
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
            'jam_kerja' => 'required|string',
            'tipe_pekerjaan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date',
        ]);

        // Create a new record in the jadwal_shift table with id_outlet set to the passed id
        JadwalShift::create([
            'jam_kerja' => $request->jam_kerja,
            'tipe_pekerjaan' => $request->tipe_pekerjaan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'id_outlet' => $id, // Storing the id in the id_outlet column
            'status' => "Waiting", // Inisiasi status Waiting
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Jadwal Shift berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        // Cari JadwalShift berdasarkan ID
        $JadwalShift = JadwalShift::where('id', $id);

        // Jika user tidak ditemukan, berikan response gagal
        if (!$JadwalShift) {
            return redirect()->route('jadwalshift')->with('error', 'Jadwal Shift tidak ditemukan');
        }

        // Hapus user
        $JadwalShift->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Jadwal Shift berhasil ditambahkan.');
    }
}