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
        // Mengambil semua data dari TipePekerjaan
        $jamShifts = JamShift::all();

        // Meneruskan data ke tampilan
        return view('jamShift', compact('jamShifts'));
    }

    public function store(Request $request)
    {
        //d($request->all());
          // Melakukan validasi
        $request->validate([
            'jam_shift' => 'required|string|max:25',
        ]);

        try {
            // Mengecek apakah data sudah ada di database
            $existingJamShift = JamShift::where('jam', $request->jam_shift)->first();

            if ($existingJamShift) {
                // Jika sudah ada munculkan pesan ini
                return redirect()->back()->with('error', 'Jam Shift sudah ada!');
            }

            // Buat instance baru dari model dan simpan ke database
            JamShift::create([
                'jam' => $request->jam_shift,
            ]);

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Jam Shift berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Mengembalikan kesalahan apapun
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'. $e->getMessage());
        }

    }

    public function editJamShift(Request $request)
    {
        $jamShift = JamShift::findOrFail($request->id);
        return view('editjamshift', compact('jamShifts'));
    }

    public function update(Request $request, $id)
    {
        // Melakukan validasi
        $request->validate([
            'jam_shift' => 'required|string|max:25',
        ]);

        try {
            // Mencari jam shift berdasarkan id
            $jamShift = JamShift::findOrFail($id);

            // Mengecek apakah data sudah ada di database
            $existingJamShift = JamShift::where('jam', $request->jam_shift)->first();

            if ($existingJamShift) {
                // Jika sudah ada munculkan pesan ini
                return redirect()->back()->with('error', 'Jam Shift sudah ada!');
            }

            // Update kolom jam shift dengan nilai baru
            $jamShift->jam_shift = $request->input('jam');

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
