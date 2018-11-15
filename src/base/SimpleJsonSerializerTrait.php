<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base;

trait SimpleJsonSerializerTrait
{
    /**
     * Serializes the current object by cutting empty arrays and NULL properties.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray(true);
    }
}
