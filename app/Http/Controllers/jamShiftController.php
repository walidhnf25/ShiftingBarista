<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\JamShift;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class jamShiftController extends Controller
{
    public function index()
    {
        $jamShift = JamShift::all()->map(function ($shift) {
            // Mengubah jam_mulai dan jam_selesai menjadi timestamp
            $mulai = strtotime($shift->jam_mulai);
            $selesai = strtotime($shift->jam_selesai);

            // Jika jam_selesai lebih kecil dari jam_mulai, tambahkan 1 hari (86400 detik)
            if ($selesai < $mulai) {
                $selesai += 86400; // 86400 detik = 24 jam
            }

            // Hitung selisih waktu dalam detik
            $selisihDetik = $selesai - $mulai;

            // Konversi selisih ke jam dan menit
            $jam = floor($selisihDetik / 3600);
            $menit = floor(($selisihDetik % 3600) / 60);

            // Format durasi dalam bentuk "X jam Y menit"
            if ($jam > 0 && $menit > 0) {
                $shift->durasi = "$jam jam $menit menit";
            } elseif ($jam > 0) {
                $shift->durasi = "$jam jam";
            } else {
                $shift->durasi = "$menit menit";
            }

            return $shift;
        });

        $apiOutlet = $this->getOutletData();

        $outletMapping = [];
        foreach ($apiOutlet as $outlet) {
            $outletMapping[$outlet['id']] = $outlet['outlet_name'];
        }
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');

        // Kirim data ke view
        return view('manager.jamShift', compact('jamShift', 'apiOutlet', 'outletMapping'));
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

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'id_outlet' => 'required',
            ]);

            // Mengecek apakah data dengan kombinasi yang sama sudah ada
            $existingJamShift = JamShift::where('jam_mulai', $request->jam_mulai)
                ->where('jam_selesai', $request->jam_selesai)
                ->where('id_outlet', $request->id_outlet)
                ->first();

            if ($existingJamShift) {
                return redirect()->back()->with('error', 'Jam Shift sudah ada!');
            }

            // Buat instance baru dari model dan simpan ke database
            JamShift::create([
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'id_outlet' => $request->id_outlet,
            ]);

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Jam Shift berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Mengembalikan kesalahan apapun
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function editJamShift(Request $request)
    {
        $jamShift = JamShift::findOrFail($request->id);
        $apiOutlet = $this->getOutletData();

        $outletMapping = [];
        foreach ($apiOutlet as $outlet) {
            $outletMapping[$outlet['id']] = $outlet['outlet_name'];
        }
        $outletMapping = collect($apiOutlet)->pluck('outlet_name', 'id');
        return view('editJamShifts', compact('jamShift', 'apiOutlet', 'outletMapping'));
    }


    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'jam_mulai' => 'required',
                'jam_selesai' => 'required',
                'id_outlet' => 'required',
            ]);

            // Mencari jam shift berdasarkan id
            $jamShift = JamShift::findOrFail($id);

            // Mengecek apakah data dengan kombinasi yang sama sudah ada
            $existingJamShift = JamShift::where('jam_mulai', $request->jam_mulai)
                ->where('jam_selesai', $request->jam_selesai)
                ->where('id_outlet', $request->id_outlet)
                ->where('id', '!=', $id) // Menghindari pencarian pada data yang sedang diperbarui
                ->first();

            if ($existingJamShift) {
                return redirect()->back()->with('error', 'Jam Shift dengan data yang sama sudah ada!');
            }

            // Update kolom jam shift dengan nilai baru
            $jamShift->jam_mulai = $request->input('jam_mulai');
            $jamShift->jam_selesai = $request->input('jam_selesai');
            $jamShift->id_outlet = $request->input('id_outlet');

            // Simpan perubahan
            $jamShift->save();

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Jam Shift berhasil diupdate.');
        } catch (\Exception $e) {
            // Mengendalikan kesalahan apapun
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Jam Shift gagal diupdate.');
        }
    }

    public function deleteJamShift(Request $request, $id)
    {
        try {
            // Temukan data berdasarkan ID dan hapus
            $jamShift = JamShift::findOrFail($id);
            $jamShift->delete();

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            // Mengendalikan kesalahan
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
