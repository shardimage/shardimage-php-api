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
use shardimage\shardimagephpapi\base\exceptions\InvalidValueException;
use shardimage\shardimagephpapi\base\caches\CacheInterface;
use shardimage\shardimagephpapi\services\dump\DumpServiceInterface;
use shardimage\shardimagephpapi\services\dump\BlackholeDumpService;

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
     * @var DumpServiceInterface
     */
    public $dumpService;

    /**
     * @var bool MsgPack support
     */
    private $useMsgPack;

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
        if (!isset($this->dumpService)) {
            $this->dumpService = new BlackholeDumpService();
        }
        if (!($this->dumpService instanceof DumpServiceInterface)) {
            throw new InvalidConfigException('The dumpService object must to implement the DumpServiceInterface!');
        }
    }

    /**
     * Setting useMsgPack value.
     * @param bool $value
     */
    public function setUseMsgPack($value)
    {
        if (!is_bool($value)) {
            throw new InvalidConfigException("The 'useMsgPack' value must to be boolean!");
        }
        if ($value && !function_exists('msgpack_pack')) {
            throw new InvalidValueException('MsgPack PHP extension not installed!');
        }
        $this->useMsgPack = $value;
    }

    /**
     * Getting useMsgPack value.
     * @return bool
     */
    public function getUseMsgPack()
    {
        return $this->useMsgPack ?? function_exists('msgpack_pack');
    }
}
