<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\requests;

use shardimage\shardimagephpapi\helpers\FileHelper;
use shardimage\shardimagephpapi\web\Http;

/**
 * File request handler.
 */
class FileRequest extends BaseRequest
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function buildUri()
    {
        return '!/file';
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getHeaders()
    {
        $size = $this->request->file->getSize();
        $name = $this->request->file->getName();
        $headers = [
            Http::HEADER_CONTENT_TYPE => Http::CONTENT_TYPE_FILE,
            $this->service->buildCustomHeader('Ref-Id') => $this->request->refId,
            $this->service->buildCustomHeader('Ref-Attribute') => $this->request->refAttribute,
        ];
        if (!empty($filename)) {
            $headers[$this->service->buildCustomHeader('Filename')] = $name;
        }
        if (isset($size)) {
            $headers[Http::HEADER_CONTENT_LENGTH] = $size;
        }

        return array_merge(parent::getHeaders(), $headers);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBody()
    {
        return $this->request->file->getResource();
    }
}
