<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JamShift;
use Illuminate\Support\Facades\Log;


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

        // Kirim data ke view
        return view('jamShift', compact('jamShift'));
    }

    public function store(Request $request)
    {
        try {
            // Mengecek apakah data sudah ada di database
            $existingJamShift = JamShift::where('jam_mulai', $request->jam_mulai)->first();
            $existingJamShift = JamShift::where('jam_selesai', $request->jam_selesai)->first();

            if ($existingJamShift) {
                // Jika sudah ada munculkan pesan ini
                return redirect()->back()->with('error', 'Jam Shift sudah ada!');
            }

            // Buat instance baru dari model dan simpan ke database
            JamShift::create([
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
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
        return view('editJamShifts', compact('jamShift'));
    }


    public function update(Request $request, $id)
    {
        try {
            // Mencari jam shift berdasarkan id
            $jamShift = JamShift::findOrFail($id);

            // Update kolom jam shift dengan nilai baru
            $jamShift->jam_mulai = $request->input('jam_mulai');
            $jamShift->jam_selesai = $request->input('jam_selesai');

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
