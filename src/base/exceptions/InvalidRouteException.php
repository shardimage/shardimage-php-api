<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base\exceptions;

/**
 * Invalid route exception.
 */
class InvalidRouteException extends Exception
{
    /**
     * {@intheritdoc}.
     *
     * @return string
     */
    public function getName()
    {
        return 'Invalid Route';
    }
}
