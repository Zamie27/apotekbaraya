<?php

namespace Midtrans;

/**
 * Midtrans Snap Class
 */
class Snap
{
    /**
     * Create Snap payment page, with this version returning full API response
     *
     * @param array $params Payment options
     * @return string Snap token
     * @throws Exception curl error or midtrans error
     */
    public static function getSnapToken($params)
    {
        $result = self::createTransaction($params);
        return $result->token;
    }
    
    /**
     * Create Snap payment page, with this version returning full API response
     *
     * @param array $params Payment options
     * @return object Snap response (token and redirect_url)
     * @throws Exception curl error or midtrans error
     */
    public static function createTransaction($params)
    {
        $payloads = array(
            'credit_card' => array(
                'secure' => Config::$is3ds
            )
        );
        
        if (array_key_exists('credit_card', $params)) {
            $payloads['credit_card'] = array_replace_recursive(
                $payloads['credit_card'], 
                $params['credit_card']
            );
        }
        
        $params = array_replace_recursive($payloads, $params);
        
        if (Config::$isSanitized) {
            Sanitizer::jsonRequest($params);
        }
        
        $result = ApiRequestor::post(
            Config::getSnapBaseUrl() . '/transactions',
            Config::$serverKey,
            $params
        );
        
        return $result;
    }
}