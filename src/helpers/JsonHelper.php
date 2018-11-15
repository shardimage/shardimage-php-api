<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\helpers;

use shardimage\shardimagephpapi\base\types\Int64;

/**
 * 32-bit safe json encoding and decoding functions.
 */
class JsonHelper
{
    /**
     * Encodes the value to a json string. Int64 objects are converted to
     * 64-bit integers.
     *
     * @param mixed $value Value
     *
     * @return string
     */
    public static function encode($value)
    {
        $hash = md5(microtime());
        self::changeInt64ToHash($value, $hash);

        return preg_replace('#"'.$hash.'\((-?\d+)\)"#isu', '\\1', json_encode($value, JSON_PRESERVE_ZERO_FRACTION));
    }

    /**
     * Decodes a json string into an array. If the current system does not
     * support 64-bit integers, those integers are converted to Int64 objects.
     *
     * @param string $json Json string
     *
     * @return mixed
     */
    public static function decode($json)
    {
        $result = json_decode($json, true);
        if (!self::isBigIntSupported()) {
            $jsonStr = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
            self::changeBigIntToInt64($jsonStr, $result);
        }

        return $result;
    }

    /**
     * Returns whether 64-bit integers are supported by the current system.
     * 
     * @return bool
     */
    private static function isBigIntSupported()
    {
        return strlen(PHP_INT_MAX) > 10;
    }

    /**
     * Recursively changes all Int64 objects into a temporary hashed string
     * in the value.
     * 
     * @param mixed  $value Value
     * @param string $hash  Replacement hash
     */
    private static function changeInt64ToHash(&$value, $hash)
    {
        if (is_array($value)) {
            foreach (array_keys($value) as $key) {
                self::changeInt64ToHash($value[$key], $hash);
            }
        } elseif ($value instanceof Int64) {
            $value = $hash.'('.$value->value.')';
        }
    }

    /**
     * Recursively changes all floats to Int64 objects, when the corresponding
     * value is a string.
     * 
     * @param mixed $strArray   Json decoded value containing strings for 64-bit integers
     * @param mixed $floatArray Json decoded value containing floats for 64-bit integers
     */
    private static function changeBigIntToInt64(&$strArray, &$floatArray)
    {
        if (is_array($strArray)) {
            foreach (array_keys($strArray) as $key) {
                self::changeBigIntToInt64($strArray[$key], $floatArray[$key]);
            }
        } elseif (is_string($strArray) && is_float($floatArray)) {
            $floatArray = new Int64([
                'value' => $strArray,
            ]);
        }
    }
}
