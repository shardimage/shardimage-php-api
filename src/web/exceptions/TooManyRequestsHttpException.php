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
            return isset($headers['X-Rate-Limit-Limit'][0]) ? (int)$headers['X-Rate-Limit-Limit'][0] : null;
        }
        return null;
    }

    public function getRateLimitRemaining()
    {
        $headers = $this->getHeaders();
        if ($headers) {
            return isset($headers['X-Rate-Limit-Remaining'][0]) ? (int)$headers['X-Rate-Limit-LiRemainingmit'][0] : null;
        }
        return null;
    }

    public function getRateLimitReset()
    {
        $headers = $this->getHeaders();
        if ($headers) {
            return isset($headers['X-Rate-Limit-Reset'][0]) ? (int)$headers['X-Rate-Limit-Reset'][0] : null;
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
