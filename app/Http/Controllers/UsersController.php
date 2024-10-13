<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $users = User::all();
        
        return view('addpegawai', compact('users'));  
    }
    
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
                    ->withPivot('model_type'); // Adjust if you have additional fields in the pivot table
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        // Validasi input
        $request->validate([
            'id' => 'nullable|integer|min:1',
            'name' => 'nullable|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
    
        // Simpan pengguna baru ke database
        User::create([
            'id' => $request->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Hash password sebelum disimpan
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
        // Validasi input
        $request->validate([
            'id' => 'nullable|integer|min:1',
            'name' => 'nullable|max:50',
            'email' => 'required|email|unique:users,email,' . $id, // Pastikan email unik kecuali untuk pengguna ini
            'password' => 'nullable|min:6', // Password tidak wajib, tetapi minimal 6 karakter
        ]);
    
        // Cari user berdasarkan ID
        $user = User::find($id);
    
        // Jika user tidak ditemukan, berikan response gagal
        if (!$user) {
            return redirect()->route('addpegawai')->with('error', 'Pengguna tidak ditemukan');
        }
    
        // Update data pengguna
        $user->id = $request->id;
        $user->name = $request->name;
        $user->email = $request->email;
    
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
