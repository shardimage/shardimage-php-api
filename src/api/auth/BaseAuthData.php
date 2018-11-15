<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\api\auth;

/**
 * Abstract class for authentication data.
 */
abstract class BaseAuthData extends \shardimage\shardimagephpapi\base\BaseObject
{
    /**
     * @var string Request URI
     */
    public $uri;

    /**
     * @var string HTTP method
     */
    public $method;

    /**
     * Returns the credentials for the authentication.
     */
    abstract public function credentials();
}
