<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use GuzzleHttp\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function staffdashboard()
    {
        // Get the logged-in user's ID
        $userId = auth()->id();

        // Get the current date and time in WIB (Asia/Jakarta timezone)
        $timezone = new \DateTimeZone('Asia/Jakarta');
        $currentDate = (new \DateTime('now', $timezone))->format('Y-m-d'); // Format: YYYY-MM-DD
        $currentTime = new \DateTime('now', $timezone);

        // Fetch all shifts for the logged-in user for today's date
        $allShiftsToday = JadwalShift::where('id_user', $userId)
            ->whereDate('tanggal', $currentDate)
            ->with('jamShift') // Assuming `jamShift` is a relationship on the Shift model
            ->get();

        // Process each shift
        $allShiftsToday->map(function ($shift) use ($currentTime, $timezone) {
            if ($shift->jamShift) {
                $shiftStartTime = new \DateTime($shift->jamShift->jam_mulai, $timezone);
                $shiftEndTime = new \DateTime($shift->jamShift->jam_selesai, $timezone);
        
                if ($currentTime < $shiftStartTime) {
                    // Shift belum dimulai
                    $shift->status = 'Akan Segera Dimulai';
                    $shift->remaining_seconds = 0; // Tidak menghitung waktu tersisa untuk selesai
                    $shift->remaining_hours = 0;
                    $shift->progress = 0;
                } elseif ($currentTime >= $shiftStartTime && $currentTime <= $shiftEndTime) {
                    // Shift sedang berjalan
                    $shift->status = 'Dalam Pengerjaan';
                    $shift->remaining_seconds = $shiftEndTime->getTimestamp() - $currentTime->getTimestamp();
                    $shift->remaining_hours = round($shift->remaining_seconds / 3600, 2);
        
                    $totalDuration = $shiftEndTime->getTimestamp() - $shiftStartTime->getTimestamp();
                    $elapsedTime = $currentTime->getTimestamp() - $shiftStartTime->getTimestamp();
                    $shift->progress = ($elapsedTime / $totalDuration) * 100;
                    $shift->progress = round($shift->progress, 2);
                } else {
                    // Shift telah selesai
                    $shift->status = 'Sudah Berakhir';
                    $shift->remaining_hours = 0;
                    $shift->progress = 100;
                }
            } else {
                // Shift tanpa jamShift
                $shift->status = 'N/A';
                $shift->remaining_hours = 0;
                $shift->progress = 0;
            }
        });
        
        // Sort the shifts by their start time (earliest first)
        $allShiftsToday = $allShiftsToday->sortBy(function ($shift) {
            return \Carbon\Carbon::parse($shift->jamShift->jam_mulai);
        });

        // Filter shifts that are valid for the current time
        $validShifts = $allShiftsToday->filter(function ($shift) use ($currentTime) {
            if ($shift->jamShift) {
                $shiftStartTime = new \DateTime($shift->jamShift->jam_mulai, new \DateTimeZone('Asia/Jakarta'));
                $shiftEndTime = new \DateTime($shift->jamShift->jam_selesai, new \DateTimeZone('Asia/Jakarta'));
                return $currentTime >= $shiftStartTime && $currentTime <= $shiftEndTime;
            }
            return false;
        });

        // Fetch outlet mapping from API or database
        $apiOutlet = $this->getOutletData(); // Ensure this method is defined
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass both variables to the view
        return view('staff.dashboard', compact('validShifts', 'allShiftsToday', 'outletMapping'));
    }

    public function showCalendar()
    {
        // Fetch the shifts where id_user is not null
        $jadwal_shifts = JadwalShift::whereNotNull('id_user')
            ->get();

        // Get outlet data from API
        $apiOutlet = $this->getOutletData();
        // Map the outlet data to create a lookup based on outlet ID
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Pass the shifts and outlet mapping to the view
        return view('manager.dashboard', compact('jadwal_shifts', 'outletMapping'));
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
