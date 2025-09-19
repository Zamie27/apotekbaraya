<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmailVerified
{
    /**
     * Handle an incoming request.
     * Check if authenticated user has verified their email
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if email is not verified
            if (is_null($user->email_verified_at)) {
                // Store user ID for verification process
                session(['verification_user_id' => $user->user_id]);
                
                // Logout user and redirect to verification page
                Auth::logout();
                
                return redirect()->route('email.verification')
                    ->with('warning', 'Email Anda belum diverifikasi. Silakan verifikasi email terlebih dahulu.');
            }
        }
        
        return $next($request);
    }
}
