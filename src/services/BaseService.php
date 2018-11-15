<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\services;

use shardimage\shardimagephpapi\base\BaseObject;
use shardimage\shardimagephpapi\base\resources\StreamResource;

/**
 * Extendable service.
 */
class BaseService extends BaseObject
{
    /**
     * @var string Backend host
     */
    public $host;

    /**
     * @var string Prefix for custom headers (e.g. X-Some-Header)
     */
    public $customHeaderPrefix;

    /**
     * @var array Resource handlers (e.g. for files)
     */
    public $resourceHandlers = [
        StreamResource::class,
    ];

    /**
     * @var string Preferred resource handler for presenting resources
     */
    public $preferredResource = StreamResource::class;

    /**
     * Builds a custom header (e.g. X-Some-Header).
     *
     * @param string $header Header
     *
     * @return string
     */
    final public function buildCustomHeader($header)
    {
        return 'X-'.(isset($this->customHeaderPrefix) ? $this->customHeaderPrefix.'-' : '').$header;
    }

    final public function parseCustomHeader($header)
    {
        if (preg_match('#^x-'.(isset($this->customHeaderPrefix) ? $this->customHeaderPrefix.'-' : '').'(.+)$#isu', $header, $match)) {
            return $match[1];
        }

        return false;
    }

    /**
     * Returns whether the provided value is file.
     *
     * @param mixed $value Value
     *
     * @return bool
     */
    public function getResourceHandler($value)
    {
        foreach ($this->resourceHandlers as $resourceHandler) {
            if ($resourceHandler::isCompatible($value)) {
                return new $resourceHandler($value);
            }
        }
    }

    /**
     * Logs an event.
     *
     * @param int    $level PHP log level
     * @param string $event Event string
     */
    public function log($level, $event)
    {
    }
}
