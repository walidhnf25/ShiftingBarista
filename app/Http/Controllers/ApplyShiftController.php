<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\JamShift;
use App\Models\TipePekerjaan;
use App\Models\Kesediaan;
use App\Models\PeriodeGaji;
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
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Ambil `tgl_mulai` dari ID terakhir di tabel periode_gaji
        $lastPeriode = PeriodeGaji::latest('id')->first(); // Mengambil record dengan ID terbesar
        $tglMulai = $lastPeriode ? $lastPeriode->tgl_mulai : null;

        // Pastikan ada `tgl_mulai`, jika tidak ada tampilkan semua jadwal
        if ($tglMulai) {
            // Base query untuk jadwal_shift, hanya menampilkan data setelah atau sama dengan `tgl_mulai`
            $jadwal_shift = JadwalShift::where('status', 'Waiting')
                            ->where('tanggal', '>=', $tglMulai);
        } else {
            $jadwal_shift = JadwalShift::where('status', 'Waiting');
        }

        // Filter berdasarkan outlet jika dipilih
        if ($request->has('id_outlet') && $request->input('id_outlet') !== '') {
            $jadwal_shift->where('id_outlet', $request->input('id_outlet'));
        }

        // Ambil hasil query setelah difilter
        $jadwal_shift = $jadwal_shift->get();

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

        // Jika avail_register adalah 'No', ambil shift dari kesediaan hanya jika tanggal setelah `tgl_mulai`
        if ($availRegister === 'No' && $tglMulai) {
            $kesediaanShifts = Kesediaan::where('id_user', $userId)
                                        ->whereHas('jadwalShift', function ($query) use ($tglMulai) {
                                            $query->where('tanggal', '>=', $tglMulai);
                                        })
                                        ->with('jadwalShift')
                                        ->get()
                                        ->pluck('jadwalShift');
        } else {
            $kesediaanShifts = collect();
        }

        // Pass all data to the view
        return view('staff.applyshift', [
            'jadwal_shift' => $jadwal_shift,
            'jamShift' => $jamShift,
            'TipePekerjaan' => $TipePekerjaan,
            'apiOutlet' => $apiOutlet,
            'outletMapping' => $outletMapping,
            'cachedJadwalShifts' => $cachedJadwalShifts,
            'availRegister' => $availRegister,
            'kesediaanShifts' => $kesediaanShifts // Pass the filtered shifts from kesediaan
        ]);
    }

    public function filterJadwalShift(Request $request)
    {
        // Ambil parameter filter dari request
        $idOutlet = $request->input('id_outlet');
        $idTipePekerjaan = $request->input('id_tipe_pekerjaan');

        // Reset tipe pekerjaan jika outlet berubah
        if ($request->has('id_outlet') && !$request->has('id_tipe_pekerjaan')) {
            $idTipePekerjaan = null;
        }

        // Base query untuk jadwal_shift
        $jadwalShift = JadwalShift::where('status', 'Waiting');

        // Filter berdasarkan outlet jika dipilih
        if ($idOutlet) {
            $jadwalShift->where('id_outlet', $idOutlet);
        }

        // Filter berdasarkan tipe pekerjaan jika dipilih
        if ($idTipePekerjaan) {
            $jadwalShift->where('id_tipe_pekerjaan', $idTipePekerjaan);
        }

        // Ambil hasil query setelah difilter
        $jadwalShift = $jadwalShift->with(['jamShift', 'tipePekerjaan'])->get();

        // Ambil data tambahan yang diperlukan
        $jamShift = JamShift::all();
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        $userId = Auth::id();
        $user = Auth::user();
        $availRegister = $user->avail_register;

        // Retrieve cached shift schedules
        $cachedIds = Cache::get("jadwal_shift_ids_user_{$userId}", []);
        $cachedJadwalShifts = [];
        foreach ($cachedIds as $cachedId) {
            $shift = Cache::get("jadwal_shift_{$userId}_{$cachedId}");
            if ($shift) {
                $cachedJadwalShifts[] = $shift;
            }
        }

        // Jika avail_register adalah 'No', ambil shift dari kesediaan
        if ($availRegister === 'No') {
            $kesediaanShifts = Kesediaan::where('id_user', $userId)
                                        ->whereHas('jadwalShift')
                                        ->get()
                                        ->pluck('jadwalShift');
        } else {
            $kesediaanShifts = collect();
        }

        // Jika id_outlet dipilih, ambil hanya tipe pekerjaan yang tersedia di outlet tersebut
        if ($idOutlet) {
            $TipePekerjaan = TipePekerjaan::whereIn('id', function ($query) use ($idOutlet) {
                $query->select('id_tipe_pekerjaan')
                    ->from('jadwal_shift')
                    ->where('id_outlet', $idOutlet);
            })->get();
        } else {
            $TipePekerjaan = TipePekerjaan::all(); // Jika tidak ada outlet yang dipilih, tampilkan semua tipe pekerjaan
        }

        // Return data ke view
        return view('staff.applyshift', [
            'jadwal_shift' => $jadwalShift,
            'jamShift' => $jamShift,
            'TipePekerjaan' => $TipePekerjaan,
            'apiOutlet' => $apiOutlet,
            'outletMapping' => $outletMapping,
            'cachedJadwalShifts' => $cachedJadwalShifts,
            'availRegister' => $availRegister,
            'kesediaanShifts' => $kesediaanShifts,
            'idOutlet' => $idOutlet, // Kirim ke view agar filter tetap aktif
            'idTipePekerjaan' => $idTipePekerjaan // Kirim ke view agar filter tetap aktif
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

        return redirect()->route('applyshift')->with('success', 'Jadwal shift berhasil ditambahkan');
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

            return redirect()->route('applyshift')->with('success', 'Jadwal Shift berhasil dihapus');

        } catch (\Exception $e) {
            \Log::error('Error removing shift from cache: ' . $e->getMessage());
            return redirect()->route('applyshift')->with('error', 'Gagal menghapus shift.');
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
            'id_jam' => $shift->jamShift ? $shift->jamShift->jam_mulai . ' - ' . $shift->jamShift->jam_selesai : 'N/A',
            'pekerjaan' => $shift->tipePekerjaan ? $shift->tipePekerjaan->tipe_pekerjaan : 'N/A',
            'hari' => \Carbon\Carbon::parse($shift->tanggal)->locale('id')->isoFormat('dddd'), // Day of the week
            'tanggal' => $shift->tanggal,
            'outletName' => $outletName,
            'aksi' => '' // Placeholder for action column, adjust as needed
        ];        

        // Return the selected shift data
        return response()->json(['jadwal_shifts' => [$shiftData]]);
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
