<?php

/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\services\dump;

/**
 * BlackholeDumpService as a default dump service, does nothing
 */
class BlackholeDumpService implements DumpServiceInterface
{

    /**
     * @param string $data
     * @param string $type
     * @return boolean
     */
    public function save($data, $type)
    {
        return true;
    }

    /**
     * @param string $value
     * @return boolean
     */
    public function setPrefix($value)
    {
        return true;
    }

    /**
     * @param Exception $exception
     */
    public function saveException($exception)
    {
        return true;
    }

}
