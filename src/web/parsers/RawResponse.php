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
        $this->decodeBody();
        $charset = $this->getCharset();
        if ($charset !== null && strcasecmp('utf-8', $charset)) {
            $this->body = mb_convert_encoding($this->body, 'utf-8', $charset);
        }
    }

    /**
     * Decoding body
     * @return void
     * @throws InvalidValueException
     */
    private function decodeBody()
    {
        if ($this->body === '') {
            $this->body = null;
        }
        if ($this->body === null || !isset($this->headers['content-encoding'])) {
            return;
        }
        $this->headers['x-original-content-encoding'] = $this->headers['content-encoding'];
        $contentEncoding = $this->headers['content-encoding'];
        switch ($contentEncoding) {
            default:
                throw new InvalidValueException('Unsupported content-encoding: ' . $contentEncoding);
            case 'gzip':
                $content = @gzdecode($this->body);
                if ($content === false) {
                    throw new InvalidValueException('Invalid content!');
                }
                $this->body = $content;
                break;
        }
        unset($this->headers['content-encoding']);
    }

    /**
     * Get charset from header
     * @return string
     */
    private function getCharset(): string
    {
        $charset = null;
        $arr = preg_split('!\s*+;\s*+!', $this->headers['content-type']);
        foreach ($arr as $param) {
            if (stripos($param, 'charset=') === 0) {
                return substr($param, strlen('charset='));
            }
        }
        return $charset;
    }
}
