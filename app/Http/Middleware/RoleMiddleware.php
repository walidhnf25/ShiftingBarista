<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::guard('user')->user(); // Get the authenticated user

        // Check if the user is authenticated and has the required role
        if ($user && $user->role === $role) {
            return $next($request); // Allow access
        }

        // Redirect if the user does not have the right role
        return redirect('/')->with(['warning' => 'Akses tidak diizinkan']);
    }
}