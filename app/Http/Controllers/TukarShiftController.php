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

class TukarShiftController extends Controller
{
    public function showOutlet(Request $request)
    {
        // Ambil semua data yang diperlukan
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $periode_gaji = PeriodeGaji::all();
        $User = User::whereIn('role', ['Staff', 'Manager'])->get();
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Ambil ID filter dari request
        $id_periode = $request->id_periode;
        $id_outlet = $request->id_outlet;

        // Default: Ambil semua shift jika tidak ada filter
        $jadwal_shift = JadwalShift::with('tipePekerjaan', 'jamShift', 'user')->whereNotNull('id_user');

        // Filter berdasarkan periode gaji jika dipilih
        if ($id_periode) {
            $periode = PeriodeGaji::find($id_periode);
            if ($periode) {
                $jadwal_shift->whereBetween('tanggal', [$periode->tgl_mulai, $periode->tgl_akhir]);
            }
        }

        // Filter berdasarkan outlet jika dipilih
        if ($id_outlet) {
            $jadwal_shift->where('id_outlet', $id_outlet);
        }

        // Ambil daftar jadwal shift yang sudah difilter
        $jadwal_shift = $jadwal_shift->get();

        // Kirim data ke view
        return view('manager.tukarshift', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'User', 'outletMapping', 'apiOutlet', 'periode_gaji', 'id_periode', 'id_outlet'));
    }

    public function filter(Request $request)
    {
        // Ambil parameter filter dari request
        $id_outlet = $request->id_outlet;
        $periode_gaji = PeriodeGaji::all();

        // Filter jadwal shift berdasarkan id_user yang tidak null dan id_outlet (jika dipilih)
        $jadwal_shift = JadwalShift::whereNotNull('id_user') // Filter untuk id_user yang tidak null
            ->when($id_outlet, function ($query, $id_outlet) {
                return $query->where('id_outlet', $id_outlet); // Filter berdasarkan outlet jika id_outlet ada
            })
            ->with(['jamShift', 'tipePekerjaan']) // Eager load relasi jamShift dan tipePekerjaan
            ->get();

        // Data tambahan untuk tampilan
        $jamShift = JamShift::all();
        $TipePekerjaan = TipePekerjaan::all();
        $User = User::whereIn('role', ['Staff', 'Manager'])->get();
        $apiOutlet = $this->getOutletData();
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Kembalikan ke view dengan data yang sudah difilter
        return view('manager.tukarshift', compact('jadwal_shift', 'jamShift', 'TipePekerjaan', 'User', 'outletMapping', 'periode_gaji'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_shift' => 'required|integer',
            'id_user' => 'required|string',
        ]);

        // Cari jadwal shift berdasarkan ID
        $jadwal_shift = JadwalShift::find($request->id_shift);

        // Jika jadwal shift tidak ditemukan, berikan response gagal
        if (!$jadwal_shift) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Shift tidak ditemukan',
            ]);
        }

        // Update data user pada jadwal shift
        $jadwal_shift->id_user = $request->id_user;
        $jadwal_shift->save();

        // Berikan response sukses
        return response()->json([
            'success' => true,
            'message' => 'User pada Jadwal Shift berhasil diupdate',
        ]);
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
