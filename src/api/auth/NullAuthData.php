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
 * Authentication without credentials.
 */
class NullAuthData extends BaseAuthData
{
    /**
     * {@inheritdoc}
     * 
     * @return array
     */
    public function credentials()
    {
        return [];
    }
}
