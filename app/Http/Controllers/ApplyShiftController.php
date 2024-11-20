<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\JamShift;
use App\Models\TipePekerjaan;
use App\Models\Kesediaan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ApplyShiftController extends Controller
{
    public function index(Request $request)
{
    $userId = Auth::id(); // Get the ID of the logged-in user
    $user = Auth::user(); // Retrieve the logged-in user instance

    // Get the avail_register status of the logged-in user
    $availRegister = $user->avail_register;

    // Retrieve all necessary data
    $jamShift = JamShift::all();
    $TipePekerjaan = TipePekerjaan::all();
    $jadwal_shift = JadwalShift::where('status', 'Waiting')->get();
    $apiOutlet = $this->getOutletData();
    $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

    // Retrieve cached shift IDs for the logged-in user
    $cachedIds = Cache::get("jadwal_shift_ids_user_{$userId}", []);
    
    // Retrieve all cached shift schedules for this user
    $cachedJadwalShifts = [];
    foreach ($cachedIds as $cachedId) {
        $shift = Cache::get("jadwal_shift_{$userId}_{$cachedId}");
        if ($shift) {
            $cachedJadwalShifts[] = $shift;
        }
    }

    // If avail_register is 'No', get shifts from kesediaan
    if ($availRegister === 'No') {
        $kesediaanShifts = Kesediaan::where('id_user', $userId)
                                     ->whereHas('jadwalShift') // Ensure there's a relationship
                                     ->get()->pluck('jadwalShift'); // Get related jadwalShift
    } else {
        $kesediaanShifts = collect(); // If not 'No', we don't need to retrieve this data
    }

    // Pass all data to the view, including the avail_register status
    return view('staff.applyshift', [
        'jadwal_shift' => $jadwal_shift,
        'jamShift' => $jamShift,
        'TipePekerjaan' => $TipePekerjaan,
        'apiOutlet' => $apiOutlet,
        'outletMapping' => $outletMapping,
        'cachedJadwalShifts' => $cachedJadwalShifts,
        'availRegister' => $availRegister,
        'kesediaanShifts' => $kesediaanShifts // Pass the shifts from kesediaan
    ]);
}


    public function getJadwalShift($id)
    {
        $userId = Auth::id();
        $cachedIds = Cache::get("jadwal_shift_ids_user_{$userId}", []);
        $cacheKeysList = Cache::get("user_{$userId}_cache_keys", []);

        if (!in_array($id, $cachedIds)) {
            $cachedIds[] = $id;
            Cache::put("jadwal_shift_ids_user_{$userId}", $cachedIds, now()->addMinutes(60));
        }

        $cacheKey = "jadwal_shift_{$userId}_{$id}";
        if (!in_array($cacheKey, $cacheKeysList)) {
            $cacheKeysList[] = $cacheKey;
            Cache::put("user_{$userId}_cache_keys", $cacheKeysList, now()->addMinutes(60));
        }

        Cache::remember($cacheKey, now()->addMinutes(60), function() use ($id) {
            return JadwalShift::find($id);
        });

        $jadwal_shifts = [];
        foreach ($cachedIds as $cachedId) {
            $jadwal_shifts[] = Cache::get("jadwal_shift_{$userId}_{$cachedId}");
        }

        return view('applyshift', ['jadwal_shifts' => $jadwal_shifts])->render();
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $cachedIds = Cache::get("jadwal_shift_ids_user_{$userId}", []);

        foreach ($cachedIds as $jadwalShiftId) {
            $jadwalShift = JadwalShift::find($jadwalShiftId);
            if ($jadwalShift) {
                Kesediaan::firstOrCreate([
                    'id_jadwal_shift' => $jadwalShiftId,
                    'id_user' => $userId,
                ]);
            }
        }

        // Ambil semua kunci cache yang disimpan pengguna
        $userCacheKeys = Cache::get("user_{$userId}_cache_keys", []);
        foreach ($userCacheKeys as $key) {
            Cache::forget($key);
        }

        // Update the avail_register column for the authenticated user
        User::where('id', $userId)->update(['avail_register' => 'No']);

        // Hapus daftar kunci cache pengguna dan cachedIds
        Cache::forget("user_{$userId}_cache_keys");
        Cache::forget("jadwal_shift_ids_user_{$userId}");

        return redirect()->back()->with('success', 'Reservasi anda berhasil didaftarkan.');
    }

    public function removeFromCache($shiftId)
    {
        try {
            $userId = Auth::id();
            
            // Retrieve cached shift IDs for the user
            $cachedIds = Cache::get("jadwal_shift_ids_user_{$userId}", []);
            
            // Remove the shift ID from the cached list if it exists
            if (($key = array_search($shiftId, $cachedIds)) !== false) {
                unset($cachedIds[$key]);
                Cache::put("jadwal_shift_ids_user_{$userId}", array_values($cachedIds));
            }

            // Forget individual shift cache
            Cache::forget("jadwal_shift_{$userId}_{$shiftId}");

            // Update the user cache keys list
            $cacheKeysList = Cache::get("user_{$userId}_cache_keys", []);
            if (($cacheKeyIndex = array_search("jadwal_shift_{$userId}_{$shiftId}", $cacheKeysList)) !== false) {
                unset($cacheKeysList[$cacheKeyIndex]);
                Cache::put("user_{$userId}_cache_keys", array_values($cacheKeysList));
            }

            // Retrieve updated shift data to render
            $jadwal_shifts = [];
            foreach ($cachedIds as $cachedId) {
                $jadwal_shifts[] = Cache::get("jadwal_shift_{$userId}_{$cachedId}");
            }

            return response()->json(['success' => true, 'jadwal_shifts' => $jadwal_shifts]);

        } catch (\Exception $e) {
            \Log::error('Error removing shift from cache: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error removing shift from cache.'], 500);
        }
    }

    public function storeAndGetJadwalShift($id)
    {
        $userId = Auth::id(); // Get the logged-in user's ID

        // Retrieve the shift data directly from the database (without caching)
        $shift = JadwalShift::with(['tipePekerjaan'])->find($id);

        if (!$shift) {
            return response()->json(['error' => 'Shift not found.'], 404);
        }

        // Fetch outlet data
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Map the outlet name based on id_outlet from the $outletMapping array
        $outletName = isset($outletMapping[$shift->id_outlet]) ? $outletMapping[$shift->id_outlet] : 'Unknown'; // Fallback to 'Unknown' if not found

        $shiftData = [
            'no' => $shift->id,
            'jam_kerja' => $shift->jam_kerja,
            'pekerjaan' => $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A',
            'tanggal' => $shift->tanggal,
            'outletName' => $outletName,
        ];

        // Return the selected shift data
        return response()->json(['jadwal_shifts' => [$shiftData]]);
    }

    public function filterJadwalShift(Request $request)
    {
        $userId = Auth::id(); // ID user yang sedang login

        // Dapatkan data outlet dari API
        $apiOutlet = $this->getOutletData();
        
        // Buat mapping antara ID outlet dan nama outlet
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Dapatkan outlet_id dari request (jika ada)
        $outletId = $request->input('outlet_id');

        // Query untuk mendapatkan shift berdasarkan status dan filter outlet_id (opsional)
        $filteredShiftsQuery = JadwalShift::where('status', 'Waiting');

        // Filter berdasarkan outlet jika outlet_id diberikan
        if ($outletId) {
            $filteredShiftsQuery->where('id_outlet', $outletId);
        }

        // Dapatkan shift yang sudah difilter
        $filteredShifts = $filteredShiftsQuery->get();

        // Generate HTML untuk menampilkan shift yang sudah difilter
        $html = '';

        // Jika tidak ada shift ditemukan, tampilkan pesan
        if ($filteredShifts->isEmpty()) {
            $html .= '<tr>';
            $html .= '<td colspan="6" class="text-center">Jadwal Shift Kosong.</td>';
            $html .= '</tr>';
        } else {
            // Looping untuk menampilkan shift yang difilter
            foreach ($filteredShifts as $index => $shift) {
                // Dapatkan nama outlet menggunakan ID outlet (jika tersedia dalam mapping)
                $outletName = $outletMapping[$shift->id_outlet] ?? 'Unknown';

                // Generate baris tabel untuk setiap shift
                $html .= '<tr>';
                $html .= '<td>' . ($index + 1) . '</td>';
                $html .= '<td>' . $shift->jam_kerja . '</td>';
                $html .= '<td>' . ($shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A') . '</td>';
                $html .= '<td>' . $shift->tanggal . '</td>';
                $html .= '<td>' . $outletName . '</td>';
                $html .= '<td><button type="button" class="btn btn-outline-primary add-to-cache" data-id="' . $shift->id . '">+</button></td>';
                $html .= '</tr>';
            }
        }

        // Kembalikan HTML untuk disisipkan ke halaman
        return response()->json($html);
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

    public function storeShift(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'jam_kerja' => 'required|string',
                'id_tipe_pekerjaan' => 'required|string',
                'tanggal' => 'required|date',
            ]);

            // Get existing shifts from cache or initialize an array
            $selectedShifts = Cache::get('selected_shifts', []);

            // Create a new shift entry
            $newShift = [
                'jam_kerja' => $request->jam_kerja,
                'id_tipe_pekerjaan' => $request->id_tipe_pekerjaan,
                'tanggal' => $request->tanggal,
            ];

            // Store the new shift
            $selectedShifts[] = $newShift;
            Cache::put('selected_shifts', $selectedShifts);

            // Return the index of the newly added shift
            return response()->json(['success' => true, 'index' => count($selectedShifts)]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            \Log::error('Error storing shift: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while adding the shift.'], 500);
        }
    }


    public function getCachedShifts()
    {
        $shifts = Cache::get('selected_shifts', []);
        return response()->json($shifts);
    }
}
