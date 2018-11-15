<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web;

use shardimage\shardimagephpapi\base\BaseObject;
use shardimage\shardimagephpapi\services\Client;

class CachedContent extends BaseObject
{
    public $method;
    public $uri;

    /**
     *
     * @var Client
     */
    public $client;
    private $hash;
    private $eTag;
    private $content;

    public function init()
    {
        $this->hash = $this->createRequestHash($this->method, $this->uri);
    }

    public function getETag()
    {
        if (!isset($this->eTag)) {
            $this->eTag = $this->getCachedETag($this->hash);
        }

        return $this->eTag;
    }

    public function set($eTag, $content)
    {
        $this->eTag = $eTag;
        $this->content = $content;
        $this->setCachedETag($this->hash, $this->eTag);
        $this->setCachedContent($this->eTag, $this->content);
    }

    public function getContent()
    {
        if (!isset($this->content)) {
            $this->content = $this->getCachedContent($this->eTag);
        }

        return $this->content;
    }

    public function delete()
    {
        $this->deleteCachedETag($this->hash);
        $this->deleteCachedContent($this->eTag);
        $this->eTag = false;
        $this->content = false;
    }

    private function getCachedETag($hash)
    {
        return $this->client->cache->get(self::createETagKey($hash));
    }

    private function setCachedETag($hash, $eTag)
    {
        $this->client->cache->set(self::createETagKey($hash), $eTag, $this->client->cacheExpiration);
    }

    private function deleteCachedETag($hash)
    {
        return $this->client->cache->delete(self::createETagKey($hash));
    }

    private function getCachedContent($eTag)
    {
        return $this->client->cache->get(self::createContentKey($eTag));
    }

    private function setCachedContent($eTag, $content)
    {
        $this->client->cache->set(self::createContentKey($eTag), $content, $this->client->cacheExpiration);
    }

    private function deleteCachedContent($eTag)
    {
        return $this->client->cache->delete(self::createContentKey($eTag));
    }

    private static function createRequestHash($method, $uri)
    {
        return md5($method . $uri);
    }

    private static function createETagKey($hash)
    {
        return 'shardimagephpapi_etag_' . $hash;
    }

    private static function createContentKey($eTag)
    {
        return 'shardimagephpapi_content_' . $eTag;
    }
}
