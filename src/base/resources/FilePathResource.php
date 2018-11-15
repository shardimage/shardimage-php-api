<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base\resources;

use shardimage\shardimagephpapi\base\resources\StreamResource;

/**
 * File path resource.
 */
class FilePathResource extends StreamResource
{
    /**
     * @param string $filePath
     */
    protected $filePath;

    public static function isCompatible($variable)
    {
        return is_string($variable) && file_exists($variable);
    }

    /**
     * @param string $filePath
     */
    public function __construct($filePath, $name = null)
    {
        $this->filePath = realpath($filePath);
        parent::__construct(fopen($filePath, 'r'), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return filesize($this->filePath);
    }

    public function delete()
    {
        fclose($this->resource);
        unlink($this->filePath);
    }

    public static function fromResource($resource, $name = null)
    {
        $stream = new StreamResource($resource);

        return new self($stream->getUrl(), $name);
    }
}
