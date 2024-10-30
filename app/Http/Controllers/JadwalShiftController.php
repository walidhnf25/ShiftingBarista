<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\JamShift;
use App\Models\TipePekerjaan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class JadwalShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Mengambil semua data dari TipePekerjaan
        $jamShift = JamShift::all();

        $TipePekerjaan = TipePekerjaan::all();
        $jadwal_shift = JadwalShift::all();

        // Meneruskan data ke tampilan
        return view('jadwalshift', compact('jadwal_shift', 'jamShift', 'TipePekerjaan'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id' => 'nullable|integer|min:1',
            'jam_kerja' => 'required|string',
            'outlet' => 'required|string',
            'tanggal' => 'required|date',
            'tipe_pekerjaan' => 'required|string',
        ]);

        // Cari user berdasarkan ID
        $jadwal_shift = JadwalShift::find($id);

        // Jika user tidak ditemukan, berikan response gagal
        if (!$jadwal_shift) {
            return redirect()->route('jadwalshift')->with('error', 'Jadwal Shift tidak ditemukan');
        }

        // Update data pengguna
        $jadwal_shift->id = $request->id;
        $jadwal_shift->jam_kerja = $request->jam_kerja;
        $jadwal_shift->outlet = $request->outlet;
        $jadwal_shift->tipe_pekerjaan = $request->tipe_pekerjaan;

        // Simpan perubahan
        $jadwal_shift->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('jadwalshift')->with('success', 'Jadwal Shift berhasil diupdate');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function store(Request $request)
    {
        // Validate the form data
        // dd($request->all());
        $request->validate([
            'jam_kerja' => 'required|string',
            'outlet' => 'required|string',
            'tanggal' => 'required|date',
            'tipe_pekerjaan' => 'required|string',
        ]);

        // Create a new record in the jadwal_shift table
        JadwalShift::create([
            'jam_kerja' => $request->jam_kerja,
            'outlet' => $request->outlet,
            'tanggal' => $request->tanggal,
            'tipe_pekerjaan' => $request->tipe_pekerjaan,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Jadwal Shift berhasil ditambahkan.');
    }



    public function destroy($id)
    {
        // Cari JadwalShift berdasarkan ID
        $JadwalShift = JadwalShift::where('id', $id);

        // Jika user tidak ditemukan, berikan response gagal
        if (!$JadwalShift) {
            return redirect()->route('jadwalshift')->with('error', 'Jadwal Shift tidak ditemukan');
        }

        // Hapus user
        $JadwalShift->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('jadwalshift')->with('success', 'Jadwal Shift berhasil dihapus');
    }
}
