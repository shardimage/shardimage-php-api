<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\exceptions;

class TooManyRequestsHttpException extends HttpException
{
    public function getRateLimitLimit()
    {
        $headers = $this->getHeaders();
        if ($headers) {
            return isset($headers['x-rate-limit-limit']) ? (int)$headers['x-rate-limit-limit'] : null;
        }
        return null;
    }

    public function getRateLimitRemaining()
    {
        $headers = $this->getHeaders();
        if ($headers) {
            return isset($headers['x-rate-limit-remaining']) ? (int)$headers['x-rate-limit-remaining'] : null;
        }
        return null;
    }

    public function getRateLimitReset()
    {
        $headers = $this->getHeaders();
        if ($headers) {
            return isset($headers['x-rate-limit-reset']) ? (int)$headers['x-rate-limit-reset'] : null;
        }
        return null;
    }

    public function getRateLimitResetAbsolut()
    {
        return time() + $this->getRateLimitReset();
    }

    public function __construct($message = null, $code = 0, $errors = null, $previous = null)
    {
        parent::__construct($message, $code, $errors, $previous, 429);
    }
}
