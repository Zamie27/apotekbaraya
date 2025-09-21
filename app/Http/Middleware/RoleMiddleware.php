<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (!$user->role || $user->role->name !== $role) {
            // Redirect to appropriate dashboard based on user's actual role
            // This prevents users from accessing other role's routes
            switch ($user->role->name) {
                case 'admin':
                    return redirect('/admin/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
                case 'apoteker':
                    return redirect('/apoteker/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
                case 'kurir':
                    return redirect('/kurir/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
                case 'pelanggan':
                default:
                    return redirect('/dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
            }
        }

        return $next($request);
    }
}
