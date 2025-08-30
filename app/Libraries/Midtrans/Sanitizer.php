<?php

namespace Midtrans;

/**
 * Midtrans Sanitizer Class
 */
class Sanitizer
{
    /**
     * Recursively remove null value
     * @param array $array
     * @return array
     */
    public static function jsonRequest($json)
    {
        return self::arrayRemoveNull($json);
    }
    
    /**
     * Recursively remove null value
     * @param array $array
     * @return array
     */
    public static function arrayRemoveNull($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::arrayRemoveNull($array[$key]);
            }
            
            if (is_null($value) || (is_array($value) && empty($value))) {
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    /**
     * Recursively remove empty string
     * @param array $array
     * @return array
     */
    public static function arrayRemoveEmptyString($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::arrayRemoveEmptyString($array[$key]);
            }
            
            if ($value === '') {
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    /**
     * Convert field name from snake_case to camelCase
     * @param array $array
     * @return array
     */
    public static function camelCase($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::camelCase($value);
            }
            $key = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            $result[$key] = $value;
        }
        return $result;
    }
}