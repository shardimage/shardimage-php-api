<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\requests;

use shardimage\shardimagephpapi\web\Http;

/**
 * Multipart request handler.
 */
class MultipartRequest extends BaseRequest
{

    /**
     * @var MultipartRequest body
     */
    protected $body;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function buildUri()
    {
        return '!/multipart/'.$this->request->multipartType;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            Http::HEADER_CONTENT_TYPE => 'multipart/'.$this->request->multipartType.'; boundary='.$this->request->boundary,
            Http::HEADER_CONTENT_TRANSFER_ENCODING => 'binary',
            Http::HEADER_CONTENT_LENGTH => strlen($this->getBody()),
            $this->service->buildCustomHeader('Mode') => $this->request->mode,
            Http::HEADER_CONTENT_ID => $this->request->id,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBody()
    {
        return $this->getBodyOrGenerate();
    }

    /**
     * To avoid multiple calling, checks the body in property, if null, then generates, stores and returns with it.
     * 
     * @return string
     */
    protected function getBodyOrGenerate()
    {
        if (is_null($this->body)) {
            $body = '';
            foreach ($this->request->requests as $request) {
                $firstLine = $request->getMethod() . ' ' . $request->getRequestTarget() . ' HTTP/' . $request->getProtocolVersion();
                $content = (string)$request->getBody();
                $headers = Http::buildHeaders($request->getHeaders());

                $multipartHeaders = Http::HEADER_CONTENT_TYPE . ': application/http' . "\r\n" . Http::HEADER_CONTENT_TRANSFER_ENCODING . ': binary' . "\r\n";

                $body .= '--' . $this->request->boundary . "\r\n" . $multipartHeaders . "\r\n" . $firstLine . "\r\n" . $headers . "\r\n" . $content . "\r\n";
            }
            $body .= '--' . $this->request->boundary . '--';

            $this->body = trim($body);
        }

        return $this->body;
    }
}
