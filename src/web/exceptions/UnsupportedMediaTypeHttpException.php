<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\exceptions;

class UnsupportedMediaTypeHttpException extends HttpException
{
    public function __construct($message = null, $code = 0, $errors = null, $previous = null)
    {
        parent::__construct($message, $code, $errors, $previous, 415);
    }
}
