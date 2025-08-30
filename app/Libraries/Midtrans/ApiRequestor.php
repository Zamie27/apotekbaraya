<?php

namespace Midtrans;

/**
 * Midtrans API Requestor Class
 */
class ApiRequestor
{
    /**
     * Send GET request
     *
     * @param string $url
     * @param string $server_key
     * @param mixed $data_hash
     * @return mixed
     * @throws Exception
     */
    public static function get($url, $server_key, $data_hash)
    {
        return self::remoteCall($url, $server_key, $data_hash, false);
    }
    
    /**
     * Send POST request
     *
     * @param string $url
     * @param string $server_key
     * @param mixed $data_hash
     * @return mixed
     * @throws Exception
     */
    public static function post($url, $server_key, $data_hash)
    {
        return self::remoteCall($url, $server_key, $data_hash, true);
    }
    
    /**
     * Actually send request to API server
     *
     * @param string $url
     * @param string $server_key
     * @param mixed $data_hash
     * @param bool $post
     * @return mixed
     * @throws Exception
     */
    public static function remoteCall($url, $server_key, $data_hash, $post = true)
    {
        $ch = curl_init();
        
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: MidtransPhp/2.0',
                'Authorization: Basic ' . base64_encode($server_key . ':')
            ),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 60
        );
        
        if ($post) {
            $curl_options[CURLOPT_POST] = 1;
            
            if ($data_hash) {
                $body = json_encode($data_hash);
                $curl_options[CURLOPT_POSTFIELDS] = $body;
            }
        } else {
            if ($data_hash) {
                $curl_options[CURLOPT_URL] .= '?' . http_build_query($data_hash);
            }
        }
        
        // merging with Config::$curlOptions
        if (count(Config::$curlOptions)) {
            // We need to combine headers manually, because it's array and it will no be merged correctly
            if (Config::$curlOptions[CURLOPT_HTTPHEADER]) {
                $mergedHeders = array_merge($curl_options[CURLOPT_HTTPHEADER], Config::$curlOptions[CURLOPT_HTTPHEADER]);
                $headerOptions = array( CURLOPT_HTTPHEADER => $mergedHeders );
            } else {
                $headerOptions = array();
            }
            
            $curl_options = array_replace_recursive($curl_options, Config::$curlOptions, $headerOptions);
        }
        
        curl_setopt_array($ch, $curl_options);
        
        $result = curl_exec($ch);
        
        if ($result === FALSE) {
            throw new Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
        } else {
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (isset($result)) {
                $result = json_decode($result);
            }
            if ($http_status >= 400) {
                $message = 'Midtrans Error (' . $http_status . '): '
                    . (isset($result->error_messages[0]) ? $result->error_messages[0] : $result->message);
                throw new Exception($message, $http_status);
            } else {
                return $result;
            }
        }
        
        curl_close($ch);
    }
}