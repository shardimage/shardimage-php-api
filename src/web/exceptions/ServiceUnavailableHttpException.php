<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\exceptions;

/**
 * ServiceUnavailableHttpException
 */
class ServiceUnavailableHttpException extends HttpException
{

    /**
     * {@inheritdoc}
     */
    public function __construct($message = null, $code = 0, $errors = null, $previous = null)
    {
        parent::__construct($message, $code, $errors, $previous, 503);
    }

    /**
     * Get Retry-After header value in seconds
     * @return int|string|null
     */
    public function getRetryAfter()
    {
        $headers = $this->getHeaders();
        $retryAfter = isset($headers['retry-after']) ? $headers['retry-after'] : null;
        return is_numeric($retryAfter) ? (int)$retryAfter : $retryAfter;
    }

    /**
     * Get absolute timestamp how long the user agent should wait before making a follow-up request
     * @return int
     */
    public function getRetryAfterAbsolute()
    {
        $retryAfter = $this->getRetryAfter();
        if (is_string($retryAfter)) {
            return strtotime($retryAfter);
        }
        return time() + $retryAfter;
    }
}
