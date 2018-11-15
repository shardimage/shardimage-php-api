<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base\resources;

/**
 * Base resource
 */
interface ResourceInterface
{
    public static function isCompatible($variable);

    public function getName();

    /**
     * @return int|null size in bytes
     */
    public function getSize();
    
    /**
     * @return resource|null resource of content
     */
    public function getResource();

    /**
     * 
     */
    public function getContent();

    /**
     * @return string|null URL of content
     */
    public function getUrl();
    
    /**
     * @return string|null MIME type
     */
    public function getMimeType();

    public function delete();

    public static function fromResource($resource, $name);
}
