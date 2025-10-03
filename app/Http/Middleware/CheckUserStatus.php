<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check if authenticated user has active status
 * Prevents inactive users from accessing protected routes
 */
class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user status is not active
            if ($user->status !== 'active') {
                // Logout the user
                Auth::logout();
                
                // Invalidate session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to login with error message
                return redirect()->route('login')
                    ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator untuk informasi lebih lanjut.');
            }
        }
        
        return $next($request);
    }
}