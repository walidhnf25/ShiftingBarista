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
            return redirect('/')->with(['warning' => 'Username atau Password salah']);
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
        $token = $response->json()['token'];

        // Dapatkan profil pengguna dari API menggunakan token
        $profileResponse = Http::withToken($token)->get('https://api-gateway.telkomuniversity.ac.id/issueprofile');

        // Jika gagal mengambil profil pengguna
        if ($profileResponse->failed()) {
            return back()->with(['warning' => 'Gagal mengambil profil pengguna']);
        }

        // Mendapatkan data profil pengguna
        $profile = $profileResponse->json();

        // Cek apakah pengguna sudah ada di database berdasarkan email
        $user = User::where('email', $profile['email'])->first();

        // Jika pengguna belum ada, buat akun baru
        if (!$user) {
            $user = new User();
            $user->username = $profile['user'];
            $user->name = $profile['fullname'];
            $user->email = $profile['email'];
            $user->no_telepon = $profile['phone']; // Menyimpan nomor telepon
            $user->role = 'Staff';  // Atur role sesuai dengan API atau kebijakan aplikasi
            $user->avail_register = 'Yes';
            $user->password = Hash::make($request->password); // Enkripsi password jika diperlukan
            $user->save();

            // Login pengguna setelah berhasil dibuat
            Auth::guard('user')->login($user);

            // Redirect berdasarkan role
            return redirect()->route('staffdashboard')->with(['success' => 'Login Berhasil.']);
        }

        // Jika pengguna sudah ada, login pengguna
        Auth::guard('user')->login($user);

        // Pengecekan role pengguna dan redirect sesuai role
        if ($user->role === 'Staff') {
            return redirect()->route('staffdashboard')->with(['success' => 'Login Berhasil.']);
        } elseif ($user->role === 'Manager') {
            return redirect()->route('managerdashboard')->with(['success' => 'Login Berhasil.']);
        }

        // Jika role tidak sesuai, logout dan beri peringatan
        Auth::guard('user')->logout();
        return redirect('/')->with(['warning' => 'Role tidak diizinkan.']);
    }

    public function registerakun(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:16',
            'password' => 'required|string', // Tanpa konfirmasi password
        ]);
    
        // Cek apakah no_telepon sudah ada di database
        $existingUser = User::where('no_telepon', $request->no_telepon)->first();
    
        if ($existingUser) {
            // Jika nomor telepon sudah terdaftar, kirimkan pesan warning dan kembali ke halaman register
            return redirect('/register')->with(['warning' => 'Nomor Telephone yang Anda masukan sudah digunakan.']);
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
