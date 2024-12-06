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

        if ($response->failed()) {
            // Jika API gagal atau kredensial salah
            return back()->with(['warning' => 'Kredensial yang Anda masukkan salah']);
        }

        // Mendapatkan token dari API
        $token = $response->json()['token'];

        // Dapatkan profil pengguna dari API menggunakan token
        $profileResponse = Http::withToken($token)->get('https://api-gateway.telkomuniversity.ac.id/issueprofile');

        if ($profileResponse->failed()) {
            // Jika gagal mengambil profil pengguna
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
            $user->role = 'Staff';  // Atur role sesuai dengan API atau kebijakan aplikasi
            $user->password = Hash::make($request->password); // Enkripsi password jika diperlukan
            $user->save();

            // Login pengguna setelah berhasil dibuat
            Auth::guard('user')->login($user);

            // Redirect berdasarkan role pengguna
            if ($user->role === 'Staff') {
                return redirect('/staffdashboard')->with(['success' => 'Akun berhasil dibuat dan login otomatis.']);
            } elseif ($user->role === 'Manager') {
                return redirect('/managerdashboard')->with(['success' => 'Akun berhasil dibuat dan login otomatis.']);
            }
        } else {
            // Jika pengguna sudah ada, login pengguna
            Auth::guard('user')->login($user);

            // Redirect berdasarkan role pengguna
            if ($user->role === 'Staff') {
                return redirect('/staffdashboard')->with(['success' => 'Login berhasil.']);
            } elseif ($user->role === 'Manager') {
                return redirect('/managerdashboard')->with(['success' => 'Login berhasil.']);
            }
        }

        // Jika role pengguna tidak sesuai, logout dan beri peringatan
        Auth::guard('user')->logout();
        return redirect('/')->with(['warning' => 'Role tidak diizinkan.']);
    }

    public function authSSO(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

          // Authenticate dengan API TelU
        $response = Http::post('https://api-gateway.telkomuniversity.ac.id/issueauth', [
            'username' => $request->username,
            'password' => $request->password,
        ]);

        if ($response->failed()) {
            return back()->with(['warning' => 'Kredensial yang anda masukan salah']);
        }

        // Ekstrak token dari respons
        $token = $response->json()['token'];


        // Dapatkan profil pengguna dari API TelU menggunakan Token
        $profileResponse = Http::withToken($token)->get('https://api-gateway.telkomuniversity.ac.id/issueprofile');


        // Profile tidak ditemukan
        if ($profileResponse->failed()) {
            return back()->with(['warning' => 'Failed to retrieve user profile.']);
        }

        // get in JSON
        $profile = $profileResponse->json();

        $user = User::where('email', $profile['email'])->first();

        if(!$user){
            $user = new User();
            $user->username = $profile['user'];
            $user->name = $profile['fullname'];
            $user->avail_register = "Yes";
            $user->email = $profile['email'];
            $user->password = Hash::make($request->password);
            $user->role = "Staff";
            $user->save();

            Auth::login($user);

            return redirect('/')->with(['success' => 'Akun SSO anda berhasil didaftarkan']);
        }

        return redirect('/registersso')->with(['warning' => 'Akun SSO Anda Telah Terdaftar.']);

    }

    public function proseslogout()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/');
        }
    }
}
