<?php

namespace App\Http\Middleware;

use App\Models\StoreSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreConfiguration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if essential store settings are configured
        $requiredSettings = [
            'store_name',
            'store_address',
            'store_latitude',
            'store_longitude',
            'shipping_rate_per_km',
            'max_delivery_distance',
            'free_shipping_minimum'
        ];

        $missingSettings = [];
        
        foreach ($requiredSettings as $setting) {
            $value = StoreSetting::get($setting);
            if (empty($value)) {
                $missingSettings[] = $setting;
            }
        }

        // If there are missing settings and user is trying to access checkout
        if (!empty($missingSettings) && $request->is('checkout')) {
            // If user is admin, redirect to settings page
            if (auth()->check() && auth()->user()->role->name === 'admin') {
                session()->flash('error', 'Pengaturan toko belum lengkap. Silakan lengkapi pengaturan terlebih dahulu.');
                return redirect()->route('admin.settings');
            }
            
            // If user is not admin, show error message and redirect to cart
            session()->flash('error', 'Sistem checkout sedang dalam konfigurasi. Silakan hubungi admin.');
            return redirect()->route('cart');
        }

        return $next($request);
    }
}