<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\exceptions;

use shardimage\shardimagephpapi\base\exceptions\Exception;

class HttpException extends Exception
{

    /**
     *
     * @var int HTTP status code
     */
    public $statusCode;

    /**
     * @var array response header-ek
     */
    private $headers = [];

    /**
     *
     * @var array|null errors
     */
    public $errors;

    /**
     * @var string|null request/response ID
     */
    private $contentId;

    public function __construct($message = null, $code = 0, $errors = null, $previous = null, $statusCode = 500, $contentId = null)
    {
        $this->statusCode = $statusCode;
        if (is_array($errors)) {
            foreach ($errors as $k => $v) {
                $errors[$k] = (array) $v;
            }
        }
        $this->errors = $errors;
        $this->contentId = $contentId;
        parent::__construct($message, (int) $code, $previous);
    }

    public function getName()
    {
        /* @TODO: HTTP namespace?? */
        return 'HTTP ' . $this->statusCode . ' ' . (isset(Http::$statuses[$this->statusCode]) ? Http::$statuses[$this->statusCode] : 'error');
    }

    /**
     * New instance of this exception
     *
     * @param int $statusCode HTTP status code
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @param \Throwable $previous
     * @throws \Throwable
     */
    public static function newInstance($statusCode, $message = null, $code = 0, $errors = null, $previous = null, $headers = null)
    {
        $map = [
            0 => self::class,
            400 => BadRequestHttpException::class,
            401 => UnauthorizedHttpException::class,
            402 => PaymentRequiredHttpException::class,
            403 => ForbiddenHttpException::class,
            404 => NotFoundHttpException::class,
            405 => MethodNotAllowedHttpException::class,
            406 => NotAcceptableHttpException::class,
            409 => ConflictHttpException::class,
            410 => GoneHttpException::class,
            415 => UnsupportedMediaTypeHttpException::class,
            416 => RangeNotSatisfiableHttpException::class,
            422 => UnprocessableEntityHttpException::class,
            423 => LockedHttpException::class,
            424 => FailedDependencyHttpException::class,
            429 => TooManyRequestsHttpException::class,
            500 => ServerErrorHttpException::class,
            501 => NotImplementedHttpException::class,
            502 => BadGatewayHttpException::class,
            503 => ServiceUnavailableHttpException::class,
        ];
        
        $statusCodeGroup = intval(floor($statusCode / 100) * 100);
        $class = $map[0];
        if (array_key_exists($statusCode, $map)) {
            $class = $map[$statusCode];
        } elseif (array_key_exists($statusCodeGroup, $map)) {
            $class = $map[$statusCodeGroup];
        }
        $obj = new $class($message, $code, $errors, $previous, $statusCode);
        if ($headers) {
            $obj->setHeaders($headers);
        }
        return $obj;
    }

    /**
     * Setting $headers property
     *
     * @param array $headers
     * @return void
     */
    public function setHeaders($headers)
    {
        $headers = array_change_key_case($headers, CASE_LOWER);
        $this->headers = $headers;
    }

    /**
     * Getting $headers property
     *
     * @return array|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set a specific header.
     * @param string $key
     * @param string $value
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Get a specific header value.
     * @param string $key
     * @return string|null
     */
    public function getHeader($key)
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * Remove a specific header.
     * @param string $key
     */
    public function removeHeader($key)
    {
        unset($this->headers[$key]);
    }

    /**
     * @return string|null
     */
    public function getContentId()
    {
        return $this->contentId;
    }
}
