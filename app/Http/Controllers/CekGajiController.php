<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CekGajiController extends Controller
{
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

    public function listOutlets(Request $request)
    {
        $apiOutlet = $this->getOutletData();
        $apiOutlet = collect($apiOutlet);

        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass data to the view
        return view('manager.chooseoutlet', compact('apiOutlet', 'outletMapping'));
    }

    public function showOutlet(Request $request, $id)
    {
        
        $apiOutlet = $this->getOutletData();

        // Find the specific outlet by ID
        $selectedOutlet = collect($apiOutlet)->firstWhere('id', $id);

        // Check if the outlet exists
        if (!$selectedOutlet) {
            abort(404, 'Outlet not found');
        }


        // Create a mapping of outlet IDs to outlet names
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass all data to the view, including apiOutlet
        return view('manager.cekgaji', compact('selectedOutlet', 'outletMapping', 'apiOutlet'));
    }

}
