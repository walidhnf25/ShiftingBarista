<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class ResetAvailController extends Controller
{
    public function index()
    {
        $users = User::all();
        // //Filter users untuk dropdown
        // $usersForDropdown = User::where('avail_register', 'No')->get();

        // Filter users untuk tabel
        $usersForTable = User::where('avail_register', 'Yes')->get();

        return view('manager.resetavail', compact('users', 'usersForTable'));
    }

    public function store(Request $request)
    {
        // Validasi inputan
        $request->validate([
            'user_id' => 'required|exists:users,id',  // Fix validation rule
        ]);

        // Ambil user_id dari inputan form
        $userId = $request->input('user_id');

        // Temukan pengguna yang sesuai
        $user = User::find($userId);

        if ($user && $user->avail_register === 'No') {
            // Update avail_register menjadi 'Yes'
            $user->avail_register = 'Yes';
            $user->save();

            return redirect()->back()->with('success', 'User availability reset successfully.');
        }

        // Return error response
        return redirect()->back()->with('error', 'User not found or already registered.');
    }
}
