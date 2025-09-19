<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Midtrans payment gateway.
    | All values are retrieved from the services configuration to maintain
    | consistency across the application.
    |
    */

    'server_key' => config('services.midtrans.server_key'),
    'client_key' => config('services.midtrans.client_key'),
    'is_production' => config('services.midtrans.is_production'),
    'is_sanitized' => config('services.midtrans.is_sanitized'),
    'is_3ds' => config('services.midtrans.is_3ds'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans URLs
    |--------------------------------------------------------------------------
    |
    | These URLs are used for different Midtrans environments.
    |
    */

    'sandbox_base_url' => 'https://api.sandbox.midtrans.com/v2',
    'production_base_url' => 'https://api.midtrans.com/v2',
    'snap_sandbox_base_url' => 'https://app.sandbox.midtrans.com/snap/v1',
    'snap_production_base_url' => 'https://app.midtrans.com/snap/v1',

];