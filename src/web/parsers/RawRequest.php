<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\parsers;

use Psr\Http\Message\RequestInterface;
use shardimage\shardimagephpapi\web\Http;

/**
 * Raw HTTP request object.
 */
class RawRequest extends BaseRawMessage
{
    /**
     * @var string HTTP method
     */
    public $method;

    /**
     * @var string Uri
     */
    public $uri;

    /**
     * @var string HTTP protocol
     */
    public $protocol;

    /**
     * Decodes the HTTP request.
     *
     * @param string|RequestInterface $message
     */
    protected function decodeMessage($message)
    {
        if ($message instanceof RequestInterface) {
            $requestLine = $message->getMethod().' '.$message->getUri().' '.$message->getProtocolVersion();
            $headers = $this->buildHeaders($message);
            $message = Http::buildHttpMessage($requestLine, $headers, $message->getBody());
        }

        return $message;
    }

    /**
     * Parses the HTTP request.
     */
    protected function parse()
    {
        list($requestLine, $request) = explode("\r\n", $this->message, 2);
        list($this->method, $this->uri, $this->protocol) = explode(' ', trim($requestLine));
        $this->headers = [];
        @list($headers, $this->body) = explode("\r\n\r\n", $request, 2);
        $this->parseHeaders($headers);
    }
}
