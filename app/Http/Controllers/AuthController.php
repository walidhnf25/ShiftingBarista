<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        // Cek kredensial login dengan guard 'user'
        if (Auth::guard('user')->attempt(['no_telepon' => $request->no_telepon, 'password' => $request->password])) {
            // Ambil pengguna yang sedang login
            $user = Auth::guard('user')->user();

            // Periksa role pengguna dan arahkan ke halaman yang sesuai
            if ($user->role === 'Staff') {
                return redirect('/staffdashboard');
            } elseif ($user->role === 'Manager') {
                return redirect('/managerdashboard');
            } else {
                // Jika role tidak sesuai, logout dan beri peringatan
                Auth::guard('user')->logout();
                return redirect('/')->with(['warning' => 'Role tidak diizinkan.']);
            }
        } else {
            // Jika login gagal
            return redirect('/')->with(['warning' => 'Nomor Telepon atau Password salah']);
        }
    }

    public function getUsers(Request $request)
    {
        // Ambil semua data user
        $users = User::all();

        // Kembalikan response JSON dengan data user
        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }

    public function LoginWithNumber(Request $request)
    {
        // Validasi input
        $request->validate([
            'no_telepon' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek kredensial login dengan guard 'user'
        if (Auth::guard('user')->attempt(['no_telepon' => $request->no_telepon, 'password' => $request->password])) {
            // Ambil pengguna yang sedang login
            $user = Auth::guard('user')->user();

            // Periksa role pengguna
            if ($user->role === 'Staff') {
                return response()->json(['message' => 'Login successful', 'redirect_url' => '/staffdashboard']);
            } elseif ($user->role === 'Manager') {
                return response()->json(['message' => 'Login successful', 'redirect_url' => '/managerdashboard']);
            } else {
                Auth::guard('user')->logout();
                return response()->json(['message' => 'Role tidak diizinkan.'], 403);
            }
        } else {
            // Jika login gagal
            return response()->json(['message' => 'Nomor Telepon atau Password salah'], 401);
        }
    }

    public function LoginWithUsername(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek kredensial login dengan guard 'user'
        if (Auth::guard('user')->attempt(['username' => $request->username, 'password' => $request->password])) {
            // Ambil pengguna yang sedang login
            $user = Auth::guard('user')->user();

            // Periksa role pengguna
            if ($user->role === 'Staff') {
                return response()->json(['message' => 'Login successful', 'redirect_url' => '/staffdashboard']);
            } elseif ($user->role === 'Manager') {
                return response()->json(['message' => 'Login successful', 'redirect_url' => '/managerdashboard']);
            } else {
                Auth::guard('user')->logout();
                return response()->json(['message' => 'Role tidak diizinkan.'], 403);
            }
        } else {
            // Jika login gagal
            return response()->json(['message' => 'Username atau Password salah'], 401);
        }
    }

    public function authSSO(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cek kredensial login menggunakan API eksternal
        $response = Http::post('https://api-gateway.telkomuniversity.ac.id/issueauth', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        // Jika API gagal atau kredensial salah
        if ($response->failed()) {
            return back()->with(['warning' => 'Kredensial yang Anda masukkan salah']);
        }

        // Mendapatkan token dari API
        $token = $response->json()['token'] ?? null;

        if (!$token) {
            return back()->with(['warning' => 'Token tidak ditemukan dari API']);
        }

        // Dapatkan profil pengguna dari API menggunakan token
        $profileResponse = Http::withToken($token)->get('https://api-gateway.telkomuniversity.ac.id/issueprofile');

        // Jika gagal mengambil profil pengguna
        if ($profileResponse->failed()) {
            return back()->with(['warning' => 'Gagal mengambil profil pengguna']);
        }

        // Mendapatkan data profil pengguna
        $profile = $profileResponse->json();

        // Ambil email jika ada, jika tidak pakai username sebagai fallback
        $emailOrUsername = $profile['email'] ?? ($profile['user'] ?? null);

        if (!$emailOrUsername) {
            \Log::error('Gagal mendapatkan email atau username dari profil:', $profile);
            return back()->with(['warning' => 'Profil tidak valid, email atau username tidak ditemukan']);
        }

        // Cek apakah pengguna sudah ada di database berdasarkan email atau username
        $user = User::where('email', $emailOrUsername)->orWhere('username', $emailOrUsername)->first();

        // Jika pengguna belum ada, buat akun baru
        if (!$user) {
            $user = new User();
            $user->username = $profile['user'] ?? $emailOrUsername;
            $user->name = $profile['fullname'] ?? $profile['user'] ?? 'Pengguna';
            $user->email = $profile['email'] ?? null;
            $user->no_telepon = $profile['phone'] ?? null;
            $user->role = 'Staff';  // Default role, bisa disesuaikan
            $user->avail_register = 'Yes';
            $user->password = Hash::make($request->password); // Password tetap di-hash
            $user->save();

            // Login pengguna setelah berhasil dibuat
            Auth::guard('user')->login($user);

            return redirect()->route('staffdashboard')->with(['success' => 'Login Berhasil.']);
        }

        // Jika pengguna sudah ada, login pengguna
        Auth::guard('user')->login($user);

        // Redirect berdasarkan role
        if ($user->role === 'Staff') {
            return redirect()->route('staffdashboard')->with(['success' => 'Login Berhasil.']);
        } elseif ($user->role === 'Manager') {
            return redirect()->route('managerdashboard')->with(['success' => 'Login Berhasil.']);
        }

        // Jika role tidak dikenali, logout
        Auth::guard('user')->logout();
        return redirect('/registersso')->with(['warning' => 'Role tidak diizinkan.']);
    }

    public function registerakun(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'password' => 'required|string', // Tanpa konfirmasi password
        ]);
    
        // Cek apakah no_telepon sudah ada di database
        $existingUser = User::where('no_telepon', $request->no_telepon)->first();
    
        if ($existingUser) {
            // Jika nomor telepon sudah terdaftar, kirimkan pesan warning dan kembali ke halaman register
            return redirect('/register')->with(['warning' => 'Akun sudah ada']);
        }
    
        // Simpan data ke dalam tabel users jika nomor belum ada
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->name;
        $user->no_telepon = $request->no_telepon;
        $user->password = Hash::make($request->password); // Mengenkripsi password
        $user->role = 'Staff';
        $user->avail_register = 'Yes';
        $user->email = null;
        $user->save();
    
        // Redirect setelah registrasi berhasil
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
    

    public function proseslogout()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/');
        }
    }
}
