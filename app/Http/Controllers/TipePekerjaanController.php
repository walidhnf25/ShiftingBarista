<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipePekerjaan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TipePekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('user')->user();
        // Mengambil semua data dari TipePekerjaan
        $TipePekerjaan = TipePekerjaan::all();

        // Meneruskan data ke tampilan
        return view('manager.tipepekerjaan', compact('TipePekerjaan', 'user'));
    }

    public function store(Request $request)
    {
        // Melakukan validasi
        $request->validate([
            'tipe_pekerjaan' => 'required|string|max:50',
            'min_fee' => 'required|numeric',    
            'avg_fee' => 'required|numeric',    
            'max_fee' => 'required|numeric',    
            'pendapatan_batas_atas' => 'required|numeric',    
            'pendapatan_batas_bawah' => 'required|numeric',    
        ]);

        try {
            // Mengecek apakah data sudah ada di database
            $existingTipePekerjaan = TipePekerjaan::where('tipe_pekerjaan', $request->tipe_pekerjaan)->first();

            if ($existingTipePekerjaan) {
                // Jika sudah ada munculkan pesan ini
                return redirect()->back()->with('error', 'Tipe Pekerjaan sudah ada!');
            }

            // Buat instance baru dari model dan simpan ke database
           $newData = $request->all();
           TipePekerjaan::create($newData);

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Tipe Pekerjaan berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Mengembalikan kesalahan apapun
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    public function editTipePekerjaan(Request $request)
    {
        $tipe_pekerjaan = TipePekerjaan::findOrFail($request->id);
        return view('editTipePekerjaan', compact('tipe_pekerjaan'));
    }

    public function update(Request $request, $id)
    {
        // Melakukan validasi
        $request->validate([
            'tipe_pekerjaan' => 'required|string|max:255',
            'min_fee' => 'required|numeric',    
            'avg_fee' => 'required|numeric',    
            'max_fee' => 'required|numeric',    
            'pendapatan_batas_atas' => 'required|numeric',    
            'pendapatan_batas_bawah' => 'required|numeric',   
        ]);

        try {
            // Mencari tipe pekerjaan berdasarkan id
            $tipe_pekerjaan = TipePekerjaan::findOrFail($id);

            // Mengecek duplikat dengan mengecualikan record yang sedang diupdate
            $existingTipePekerjaan = TipePekerjaan::where('tipe_pekerjaan', $request->tipe_pekerjaan)
                ->where('id', '!=', $id)  // Tambahan ini untuk mengecualikan record yang sedang diupdate
                ->first();

            if ($existingTipePekerjaan) {
                // Jika nama tipe pekerjaan sudah ada di record lain
                return redirect()->back()->with('error', 'Tipe Pekerjaan sudah ada!');
            }

            // Update kolom tipe pekerjaan dengan nilai baru
            $tipe_pekerjaan->tipe_pekerjaan = $request->input('tipe_pekerjaan');
            $tipe_pekerjaan->min_fee = $request->input('min_fee');
            $tipe_pekerjaan->avg_fee = $request->input('avg_fee');
            $tipe_pekerjaan->max_fee = $request->input('max_fee');
            $tipe_pekerjaan->pendapatan_batas_atas = $request->input('pendapatan_batas_atas');
            $tipe_pekerjaan->pendapatan_batas_bawah = $request->input('pendapatan_batas_bawah');

            // Simpan perubahan
            $tipe_pekerjaan->save();

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Tipe Pekerjaan berhasil diupdate.');
        } catch (\Exception $e) {
            // Mengendalikan kesalahan apapun
            return redirect()->back()->with('error', 'Tipe Pekerjaan gagal diupdate.');
        }
    }

    public function deleteTipePekerjaan($id)
    {
        try {
            // Temukan data berdasarkan ID dan hapus
            $tipePekerjaan = TipePekerjaan::findOrFail($id);
            $tipePekerjaan->delete();

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            // Mengendalikan kesalahan
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
