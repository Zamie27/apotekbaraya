<?php

namespace Midtrans;

/**
 * Midtrans Configuration Class
 */
class Config
{
    /**
     * Your merchant's server key
     * @static
     */
    public static $serverKey;
    
    /**
     * Your merchant's client key
     * @static
     */
    public static $clientKey;
    
    /**
     * True for production
     * false for sandbox mode
     * @static
     */
    public static $isProduction = false;
    
    /**
     * Set it true to enable 3D Secure by default
     * @static
     */
    public static $is3ds = true;
    
    /**
     * Enable request/response sanitization
     * @static
     */
    public static $isSanitized = true;
    
    /**
     * Default options for every request
     * @static
     */
    public static $curlOptions = array();
    
    const SANDBOX_BASE_URL = 'https://api.sandbox.midtrans.com/v2';
    const PRODUCTION_BASE_URL = 'https://api.midtrans.com/v2';
    const SNAP_SANDBOX_BASE_URL = 'https://app.sandbox.midtrans.com/snap/v1';
    const SNAP_PRODUCTION_BASE_URL = 'https://app.midtrans.com/snap/v1';
    
    /**
     * Get base URL
     * @return string
     */
    public static function getBaseUrl()
    {
        return Config::$isProduction ?
            Config::PRODUCTION_BASE_URL : Config::SANDBOX_BASE_URL;
    }
    
    /**
     * Get snap base URL
     * @return string
     */
    public static function getSnapBaseUrl()
    {
        return Config::$isProduction ?
            Config::SNAP_PRODUCTION_BASE_URL : Config::SNAP_SANDBOX_BASE_URL;
    }
}