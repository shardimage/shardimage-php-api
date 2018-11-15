<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\base\resources;

use shardimage\shardimagephpapi\base\resources\FilePathResource;
use shardimage\shardimagephpapi\base\resources\StreamResource;

/**
 * Binary string resource
 */
class BinaryStringResource extends StreamResource
{
    /**
     * @var int size of content
     */
    protected $size;

    public static function isCompatible($variable)
    {
        return is_string($variable) && !mb_check_encoding($variable, 'utf-8') && !FilePathResource::isCompatible($variable);
    }

    /**
     * @param string $binaryString binary data
     */
    public function __construct($binaryString)
    {
        $this->size = strlen($binaryString);
        parent::__construct(fopen('data://text/plain;base64,' . base64_encode($binaryString), 'r'));
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getMimeType()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return $this->size;
    }

    public function delete()
    {
        fclose($this->resource);
    }

    public static function fromResource($resource, $name = null)
    {
        $stream = new StreamResource($resource);

        return new self(file_get_contents($stream->getUrl()), $name);
    }
}
