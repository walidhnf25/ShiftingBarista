<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use GuzzleHttp\Client;

class WaktuShiftController extends Controller
{
    public function showCalendar()
{
    $userId = auth()->id(); // Get the logged-in user's ID

    // Fetch the shifts for the logged-in user
    $jadwal_shifts = JadwalShift::where('id_user', $userId)
        ->get();

    // Get outlet data from API
    $apiOutlet = $this->getOutletData();
    // Map the outlet data to create a lookup based on outlet ID
    $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

    // Pass the shifts and outlet mapping to the view
    return view('staff.waktushift', compact('jadwal_shifts', 'outletMapping'));
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

}
