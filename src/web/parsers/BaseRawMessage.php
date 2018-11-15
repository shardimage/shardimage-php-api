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
use Psr\Http\Message\ResponseInterface;
use shardimage\shardimagephpapi\base\exceptions\InvalidCallException;
use shardimage\shardimagephpapi\services\BaseService;
use shardimage\shardimagephpapi\web\Http;

/**
 * Raw HTTP message object.
 */
class BaseRawMessage
{
    /**
     * @var string HTTP message
     */
    public $message;

    /**
     * @var BaseService Service
     */
    protected $service;

    /**
     * @var string[] Headers
     */
    public $headers = [];

    /**
     * @var string[] Custom headers (e.g. X-Some-Header)
     */
    public $customHeaders = [];

    /**
     * @var string Body
     */
    public $body;

    /**
     * @var string[] Body segments of a multipart message
     */
    public $parts;

    /**
     * @var string Content type
     */
    public $type;

    /**
     * @var string Boundary of a multipart message
     */
    public $boundary;

    /**
     * Creates and parses a HTTP message.
     * 
     * @param BaseService                               $service Service
     * @param string|RequestInterface|ResponseInterface $message
     */
    public function __construct($service, $message)
    {
        $this->service = $service;
        $this->message = $this->decodeMessage($message);
        $this->parse();
        $this->type = $this->getType();
        $this->boundary = $this->getBoundary();
        $this->parts = $this->getParts();
    }

    /**
     * Returns the content type.
     * 
     * @return string
     */
    protected function getType()
    {
        $contentType = $this->headers['content-type'];
        $parts = explode(';', $contentType, 2);

        return trim($parts[0]);
    }

    /**
     * Returns the boundary of a multipart message.
     *
     * @return string|null
     */
    protected function getBoundary()
    {
        if (preg_match('#(?:;[ ]*)boundary=(["]?)(?<boundary>[\w\'()+\\.,\/:=? -]{1,69}[\w\'()+\\.,\/:=?-])(\1)(?:[ ]*(?:;|$))#', $this->headers['content-type'], $match)) {
            return $match['boundary'];
        }
    }

    /**
     * Returns the body segments of a multipart message.
     * 
     * @return string[]
     */
    protected function getParts()
    {
        $result = [];
        if ($this->boundary === null) {
            $parts = [$this->body];
        } else {
            $parts = explode("\r\n--".$this->boundary, "\r\n".$this->body);
        }

        foreach (array_slice($parts, 1, count($parts) - 2) as $part) {
            list(, $part) = explode("\r\n\r\n", $part, 2);
            $result[] = $part;
        }

        return $result;
    }

    /**
     * Parses the headers into arrays.
     *
     * @param string $headers
     */
    protected function parseHeaders($headers)
    {
        $prefix = strtolower($this->service->buildCustomHeader(''));
        $prefixLength = strlen($prefix);
        $headers = explode("\r\n", $headers);
        foreach ($headers as $rawHeader) {
            list($header, $value) = explode(':', $rawHeader, 2);
            $header = strtolower($header);
            $value = trim($value);
            $this->headers[$header] = $value;
            if (substr($header, 0, $prefixLength) == $prefix) {
                $this->customHeaders[substr($header, $prefixLength)] = $value;
            }
        }
        if (!isset($this->headers['content-type'])) {
            $this->headers['content-type'] = 'application/json';
        }
    }

    /**
     * Builds the headers string from a PSR message.
     *
     * @param \Psr\Http\Message\RequestInterface|\Psr\Http\Message\ResponseInterface $message
     *
     * @return string
     */
    protected function buildHeaders($message)
    {
        return Http::buildHeaders($message->getHeaders());
    }

    /**
     * Returns whether the message is a file.
     *
     * @return bool
     */
    public function isFile()
    {
        return in_array($this->type, [
            Http::CONTENT_TYPE_FILE,
        ]);
    }

    /**
     * Decodes a message.
     *
     * @param string|RequestInterface|ResponseInterface $message
     *
     * @throws InvalidCallException
     */
    protected function decodeMessage($message)
    {
        throw new InvalidCallException(__METHOD__.' must be implemented!');
    }

    /**
     * Parses the message.
     * 
     * @throws InvalidCallException
     */
    protected function parse()
    {
        throw new InvalidCallException(__METHOD__.' must be implemented!');
    }
}
