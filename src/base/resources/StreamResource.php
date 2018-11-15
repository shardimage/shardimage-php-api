<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base\resources;

use shardimage\shardimagephpapi\helpers\FileHelper;

/**
 * Stream resource.
 */
class StreamResource implements ResourceInterface
{
    /**
     * @var resource
     */
    protected $resource;
    protected $name;

    public static function isCompatible($variable)
    {
        return is_resource($variable);
    }

    public function __construct($resource, $name = null)
    {
        $this->resource = $resource;
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function getContent()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return FileHelper::getFilesize($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return FileHelper::getFilename($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType()
    {
        return FileHelper::getMimeType($this->resource);
    }

    public function delete()
    {
        $url = $this->getUrl();
        fclose($this->resource);
        unlink($url);
    }

    public static function fromResource($resource, $name = null)
    {
        return new self($resource, $name);
    }
}
