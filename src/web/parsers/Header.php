<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\parsers;

/**
 * HTTP header parser object.
 */
class Header
{
    /**
     * @var string Value of the header
     */
    public $value;

    /**
     * @var array Additional parameters in the header
     */
    public $params = [];

    /**
     * Creates and parses the header.
     *
     * @param string $value Header value
     */
    public function __construct($value)
    {
        if (is_string($value)) {
            $values = explode(';', trim($value));
            foreach ($values as $id => $value) {
                if ($id == 0) {
                    $this->value = trim($value);
                } else {
                    @list($key, $param) = explode('=', $value, 2);
                    if (!isset($param)) {
                        $param = '1';
                    }

                    $this->params[trim($key)] = trim($param, ' "');
                }
            }
        }
    }

    /**
     * Returns the value of an additional named parameter.
     *
     * @param string $param
     * @param mixed  $default
     *
     * @return string|mixed
     */
    public function getParam($param, $default = null)
    {
        return array_key_exists($param, $this->params) ? $this->params[$param] : $default;
    }
}
