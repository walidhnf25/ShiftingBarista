<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        // Periksa apakah user login dan memiliki salah satu role yang diizinkan
        if (!$user || !in_array($user->role, $roles)) {
            return redirect()->route('unauthorized'); // Sesuaikan dengan route unauthorized Anda
        }

        return $next($request);
    }
}
