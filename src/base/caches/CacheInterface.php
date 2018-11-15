<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base\caches;

/**
 * Base cache.
 */
interface CacheInterface
{
    public function set($key, $value, $expiration = 0);

    public function get($key);

    public function delete($key);
}
