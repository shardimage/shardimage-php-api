<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\parsers;

use Psr\Http\Message\ResponseInterface;
use shardimage\shardimagephpapi\web\Http;
use shardimage\shardimagephpapi\base\exceptions\InvalidValueException;

/**
 * Raw HTTP response object.
 */
class RawResponse extends BaseRawMessage
{
    /**
     * @var string HTTP status code
     */
    public $statusCode;

    /**
     * @var string HTTP status
     */
    public $statusText;

    /**
     * @var string HTTP protocol
     */
    public $protocol;

    /**
     * Decodes the HTTP response.
     * 
     * @param string|ResponseInterface $message
     */
    protected function decodeMessage($message)
    {
        if ($message instanceof ResponseInterface) {
            $statusLine = 'HTTP/' . $message->getProtocolVersion() . ' ' . $message->getStatusCode() . ' ' . $message->getReasonPhrase();
            $headers = $this->buildHeaders($message);
            $body = $message->getBody();
            $message = Http::buildHttpMessage($statusLine, $headers, $body);
        }

        return $message;
    }

    /**
     * Parses the HTTP response.
     */
    protected function parse()
    {
        list($statusLine, $request) = explode("\r\n", $this->message, 2);
        list($this->protocol, $this->statusCode, $this->statusText) = explode(' ', trim($statusLine), 3);
        $this->headers = [];
        @list($headers, $this->body) = explode("\r\n\r\n", $request, 2);
        $this->parseHeaders($headers);
        if (isset($this->headers['content-encoding'])) {
            $this->body = $this->decodeBody($this->headers['content-encoding'], $this->body);
            $this->headers['x-original-content-encoding'] = $this->headers['content-encoding'];
            unset($this->headers['content-encoding']);
        }
    }

    private function decodeBody($encoding, $body)
    {
        switch ($encoding) {
            case 'gzip':
                return gzdecode($body);
        }

        throw new InvalidValueException('Unsupported content-encoding: ' . $encoding);
    }
}
