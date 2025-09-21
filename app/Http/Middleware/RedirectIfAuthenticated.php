<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect to appropriate dashboard based on role
                switch ($user->role->name) {
                    case 'admin':
                        return redirect('/admin/dashboard');
                    case 'apoteker':
                        return redirect('/apoteker/dashboard');
                    case 'kurir':
                        return redirect('/kurir/dashboard');
                    case 'pelanggan':
                    default:
                        return redirect('/dashboard');
                }
            }
        }

        return $next($request);
    }
}
