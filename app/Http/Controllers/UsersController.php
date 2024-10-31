<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return view('addpegawai', compact('users'));
    }

    // public function roles(): BelongsToMany
    // {
    //     return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
    //                 ->withPivot('model_type'); // Adjust if you have additional fields in the pivot table
    // }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input termasuk unik untuk username
        $request->validate([
            'name' => 'required|max:50',
            'username' => 'required|max:50|unique:users,username', // Tambah validasi unique untuk username
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|max:255',
        ]);

        // Simpan pengguna baru ke database
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash password sebelum disimpan
            'role' => $request->role,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('addpegawai')->with('success', 'Pengguna berhasil ditambahkan');
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input termasuk unik untuk username kecuali pengguna saat ini
        $request->validate([
            'id' => 'nullable|integer|min:1',
            'name' => 'nullable|max:50',
            'username' => 'required|max:50|unique:users,username,' . $id, // Validasi unique username, kecuali untuk pengguna ini
            'email' => 'required|email|unique:users,email,' . $id, // Validasi unik email kecuali untuk pengguna ini
            'password' => 'nullable|min:6', // Password tidak wajib, tetapi minimal 6 karakter
            'role' => 'required|max:255',
        ]);

        // Cari user berdasarkan ID
        $user = User::find($id);

        // Jika user tidak ditemukan, berikan response gagal
        if (!$user) {
            return redirect()->route('addpegawai')->with('error', 'Pengguna tidak ditemukan');
        }

        // Update data pengguna
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;

        // Jika password diisi, maka update password
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Simpan perubahan
        $user->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('addpegawai')->with('success', 'Pengguna berhasil diupdate');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($email)
    {
        // Cari user berdasarkan ID
        $user = User::where('email', $email);

        // Jika user tidak ditemukan, berikan response gagal
        if (!$user) {
            return redirect()->route('addpegawai')->with('error', 'Pengguna tidak ditemukan');
        }

        // Hapus user
        $user->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('addpegawai')->with('success', 'Pengguna berhasil dihapus');
    }

}
