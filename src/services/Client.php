<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\services;

use shardimage\shardimagephpapi\api\auth\NullAuthData;
use shardimage\shardimagephpapi\base\exceptions\InvalidConfigException;
use shardimage\shardimagephpapi\base\caches\CacheInterface;

/**
 * Client service.
 */
class Client extends BaseService
{
    /**
     * @var string Authentication class
     */
    public $authData = NullAuthData::class;

    /**
     * @var array Custom HTTP headers (x-...)
     */
    public $customHeaders = [];

    /**
     * @var array Additional HTTP headers
     */
    public $headers = [];

    /**
     * @var bool MsgPack support
     */
    public $useMsgPack = true;

    /**
     * @var Gzip compression support
     */
    public $useGzip = true;

    /**
     * @var string|array Proxy or an array of proxies per protocol
     */
    public $proxy;

    /**
     * @var CacheInterface
     */
    public $cache;

    /**
     * @var int
     */
    public $cacheExpiration = 0;

    /**
     * @var int Request timeout [sec]
     */
    public $timeout = 180;

    /**
     * @var string
     */
    public $acceptLanguage;

    /**
     * Initializes the client data.
     * 
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset($this->host)) {
            throw new InvalidConfigException('API host must be specified!');
        }
    }
}
