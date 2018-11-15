<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\helpers;

use shardimage\shardimagephpapi\base\BaseObject;


/**
 * Helper methods for general API purposes.
 */
class ApiHelper
{

    /**
     * Generates a random unique ID.
     *
     * @return string
     */
    public static function generateId()
    {
        return getmypid() . '.' . microtime(true) . '.' . mt_rand();
    }

    /**
     * Replaces unsafe values in an array with NULL (e.g. resources, objects).
     * 
     * @param array $array Array
     *
     * @return array
     */
    public static function maskUnsafeAttributes($array)
    {
        if (is_array($array)) {
            foreach ($array as &$value) {
                if (self::isUnsafeValue($value)) {
                    $value = '';
                } elseif (is_array($value)) {
                    foreach ($value as &$_value) {
                        if (self::isUnsafeValue($_value)) {
                            $_value = '';
                        }
                    }
                }
            }
        }

        return $array;
    }

    /**
     * Returns whether a value is considered to be unsafe (e.g. for JSON).
     *
     * @param mixed $value Value
     *
     * @return bool
     */
    private static function isUnsafeValue($value)
    {
        return !is_string($value) && !is_int($value) && !is_float($value) && !is_bool($value) && !is_array($value) && !is_null($value) && !($value instanceof BaseObject);
    }

    /**
     * Camelizes a string removig all non-alphanumeric characters and making it camelcase.
     *
     * @param string $string String
     *
     * @return string
     */
    public static function camelize($string)
    {
        return str_replace(' ', '', ucwords(preg_replace('/[^A-Za-z0-9]+/', ' ', $string)));
    }

    /**
     * Cleans an array, removing NULL fields and empty subarrays.
     * 
     * @param array $array Array
     *
     * @return array
     */
    public static function cleanup($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                if (is_array($val)) {
                    $array[$key] = self::cleanup($val);
                    if (empty($array[$key])) {
                        unset($array[$key]);
                    }
                } elseif (!isset($array[$key])) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

}
