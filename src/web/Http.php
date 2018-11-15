<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web;

/**
 * HTTP helper methods and constants.
 */
class Http
{
    const STATUS_OK = 200;
    const STATUS_CREATED = 201;
    const STATUS_ACCEPTED = 202;
    const STATUS_NO_CONTENT = 204;
    const STATUS_MOVED_PERMANENTLY = 301;
    const STATUS_FOUND = 302;
    const STATUS_NOT_MODIFIED = 304;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_INTERNAL_SERVER_ERROR = 500;

    /**
     * File content type.
     */
    const CONTENT_TYPE_FILE = 'application/octet-stream';

    /**
     * Json content type.
     */
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * Content length header.
     */
    const HEADER_CONTENT_LENGTH = 'Content-Length';

    /**
     * Content Type header.
     */
    const HEADER_CONTENT_TYPE = 'Content-Type';

    /**
     * Content ID header.
     */
    const HEADER_CONTENT_ID = 'Content-ID';

    /**
     * Content transfer encoding header.
     */
    const HEADER_CONTENT_TRANSFER_ENCODING = 'Content-Transfer-Encoding';

    /**
     * Accept language header.
     */
    const HEADER_ACCEPT_LANGUAGE = 'Accept-Language';

    /**
     * Accept encoding header.
     */
    const HEADER_ACCEPT_ENCODING = 'Accept-Encoding';

    /**
     * Accept header.
     */
    const HEADER_ACCEPT = 'Accept';

    /**
     * Expect header.
     */
    const HEADER_EXPECT = 'Expect';

    /**
     * ETag header.
     */
    const HEADER_IF_NONE_MATCH = 'If-None-Match';

    public static $statuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Builds the headers from an array.
     * 
     * @param array $headers Headers in array
     *
     * @return string
     */
    public static function buildHeaders($headers)
    {
        $result = '';
        foreach ($headers as $header => $values) {
            $result .= $header.':'.implode(';', $values)."\r\n";
        }

        return $result;
    }

    /**
     * Builds the HTTP message.
     * 
     * @param string $firstline Request/status line
     * @param string $headers   HTTP headers
     * @param mixed  $body      HTTP body
     *
     * @return string
     */
    public static function buildHttpMessage($firstline, $headers, $body)
    {
        return $firstline."\r\n".$headers."\r\n".(string) $body;
    }
}
