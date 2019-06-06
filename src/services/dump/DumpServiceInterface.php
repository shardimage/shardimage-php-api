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
 * Interface for the dumping services
 */
interface DumpServiceInterface
{

    const DUMPTYPE_REQUEST = "request";
    const DUMPTYPE_RESPONSE = "response";

    /**
     * @param string $data
     * @param string $type
     */
    public function save($data, $type);

    /**
     * @param Exception $exception
     */
    public function saveException($exception);

    /**
     * @param string $value
     */
    public function setPrefix($value);
}
