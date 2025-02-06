<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeriodeGaji;

class PeriodeGajiController extends Controller
{
    public function index(Request $request)
    {
        // Eager load TipePekerjaan with JadwalShift
        $periodegaji = PeriodeGaji::all();

        return view('manager.periodegaji', compact('periodegaji'));
    }

    public function store(Request $request)
    {
        // Validasi dasar untuk memastikan semua field terisi
        $request->validate([
            'nama_periode_gaji' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_akhir' => 'required|date',
        ]);

        try {
            // Validasi tanggal akhir tidak boleh sebelum tanggal mulai
            if (strtotime($request->tgl_akhir) < strtotime($request->tgl_mulai)) {
                return redirect()
                    ->back()
                    ->with('error', 'Tanggal akhir tidak boleh sebelum tanggal mulai!');
            }

            //  overlap tanggal dengan periode yang sudah ada
            $existingPeriod = PeriodeGaji::where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    // Cek apakah ada periode yang overlap
                    $q->whereBetween('tgl_mulai', [$request->tgl_mulai, $request->tgl_akhir])
                    ->orWhereBetween('tgl_akhir', [$request->tgl_mulai, $request->tgl_akhir]);
                })->orWhere(function($q) use ($request) {
                    // Cek apakah ada periode yang mencakup rentang tanggal baru
                    $q->where('tgl_mulai', '<=', $request->tgl_mulai)
                    ->where('tgl_akhir', '>=', $request->tgl_akhir);
                });
            })->first();

            if ($existingPeriod) {
                return redirect()
                    ->back()
                    ->with('error', 'Rentang tanggal konflik sudah diambil');
            }

            // Validasi nama periode harus unik
            $existingName = PeriodeGaji::where('nama_periode_gaji', $request->nama_periode_gaji)
                ->exists();

            if ($existingName) {
                return redirect()
                    ->back()
                    ->with('error', 'Nama periode sudah digunakan!');
            }

            PeriodeGaji::create([
                'nama_periode_gaji' => $request->nama_periode_gaji,
                'tgl_mulai' => $request->tgl_mulai,
                'tgl_akhir' => $request->tgl_akhir,
            ]);

            return redirect()
                ->route('periodegaji.index')
                ->with('success', 'Periode Gaji berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error creating periode gaji: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.' . $e->getMessage());
        }
    }

    
    public function editPeriodeGaji(Request $request)
    {
        $periode_gaji = PeriodeGaji::find($request->id);
    
        if (!$periode_gaji) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    
        return view('editPeriodeGaji', compact('periode_gaji'));
    }

    public function update(Request $request, $id)
    {
        // Validasi dasar untuk memastikan semua field terisi
        $request->validate([
            'nama_periode_gaji' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_akhir' => 'required|date',
        ]);

        try {

            // Mencari tipe pekerjaan berdasarkan id
            $periode_gaji = PeriodeGaji::findOrFail($id);

             // Cek apakah nama periode sudah digunakan oleh entitas lain
            $existingName = PeriodeGaji::where('nama_periode_gaji', $request->nama_periode_gaji)
            ->where('id', '!=', $id) // Pastikan tidak mengecek dirinya sendiri
            ->exists();

            if ($existingName) {
                return redirect()
                    ->back()
                    ->with('error', 'Nama periode sudah digunakan!');
            }

            // Validasi tanggal akhir tidak boleh sebelum tanggal mulai
            if (strtotime($request->tgl_akhir) < strtotime($request->tgl_mulai)) {
                return redirect()
                    ->back()
                    ->with('error', 'Tanggal akhir tidak boleh sebelum tanggal mulai!');
            }


             // Cek apakah rentang tanggal bertabrakan dengan periode lain
            $existingPeriod = PeriodeGaji::where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    // Cek apakah ada periode yang overlap
                    $q->whereBetween('tgl_mulai', [$request->tgl_mulai, $request->tgl_akhir])
                        ->orWhereBetween('tgl_akhir', [$request->tgl_mulai, $request->tgl_akhir]);
                })->orWhere(function ($q) use ($request) {
                    // Cek apakah ada periode yang mencakup rentang tanggal baru
                    $q->where('tgl_mulai', '<=', $request->tgl_mulai)
                        ->where('tgl_akhir', '>=', $request->tgl_akhir);
                });
            })
            ->where('id', '!=', $id) // Pastikan tidak mengecek dirinya sendiri
            ->exists();

            if ($existingPeriod) {
                return redirect()
                    ->back()
                    ->with('error', 'Rentang tanggal sudah diambil!');
            }

            $periode_gaji->nama_periode_gaji = $request->input('nama_periode_gaji');
            $periode_gaji->tgl_mulai = $request->input('tgl_mulai');
            $periode_gaji->tgl_akhir = $request->input('tgl_akhir');

            $periode_gaji->save();
            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Tipe Pekerjaan berhasil diupdate.');

        } catch  (\Exception $e) {
            // Mengendalikan kesalahan apapun
            return redirect()->back()->with('error', 'Tipe Pekerjaan gagal diupdate.');
        }

    }

    public function delete($id)
    {
        try {
            // Temukan data berdasarkan ID dan hapus
            $PeriodeGaji = PeriodeGaji::findOrFail($id);
            $PeriodeGaji->delete();

            // Mengembalikan respon sukses
            return redirect()->back()->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            // Mengendalikan kesalahan
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }


}
