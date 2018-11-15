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
 * Invalid parameter exception.
 */
class InvalidParamException extends \BadMethodCallException
{
    /**
     * {@intheritdoc}.
     *
     * @return string
     */
    public function getName()
    {
        return 'Invalid Parameter';
    }
}
