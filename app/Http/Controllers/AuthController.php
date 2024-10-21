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
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
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
            return back()->with(['warning' => 'Login failed. Please check your credentials and try again.']);
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
            $user->username = $profile['fullname'];
            $user->email = $profile['email'];
            $user->password = Hash::make($request->password);

            $user->save();
        }

        // User Login
        Auth::login($user);

        return redirect()->route('index');
    }

    public function proseslogout()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/');
        }
    }
}
