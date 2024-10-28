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
        if (Auth::guard('user')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect('/index');
        } else {
            return redirect('/')->with(['warning' => 'Email atau Password salah']);
        }
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
            return back()->with(['warning' => 'Pendaftaran Gagal. Kredensial yang anda masukan salah']);
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
            $user->email = $profile['email'];
            $user->password = Hash::make($request->password);
            $user->role = "Staff";
            $user->save();

            Auth::login($user);

            return redirect('/')->with(['success' => 'Akun SSO anda berhasil didaftarkan']);
            return redirect('/')->with(['success' => 'Akun SSO anda berhasil didaftarkan']);;
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
