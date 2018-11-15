<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\api;

use shardimage\shardimagephpapi\base\BaseObject;

/**
 * Error object for a request.
 *
 * Stores all relevant data for the exception thrown in the backend or other user errors.
 */
class ResponseError extends BaseObject implements \JsonSerializable
{
    use \shardimage\shardimagephpapi\base\SimpleJsonSerializerTrait;
    /**
     * General error/exception.
     */
    const ERRORCODE_GENERAL_EXCEPTION = 0;

    /**
     * Model validation error in the backend.
     */
    const ERRORCODE_VALIDATION_FAILURE = 1000;

    /**
     * Model missing in the backend.
     */
    const ERRORCODE_OBJECT_NOT_FOUND = 1001;

    /**
     * Access denied.
     */
    const ERRORCODE_ACCESS_DENIED = 1002;

    /**
     * HTTP error.
     */
    const ERRORCODE_HTTP_ERROR = 1003;

    /**
     * @var string Exception type
     */
    public $type;

    /**
     * @var int Exception code
     */
    public $code;

    /**
     * @var string Exception message
     */
    public $message;

    /**
     * @var string Exception source
     */
    public $file;

    /**
     * @var string Exception source
     */
    public $line;

    /**
     * @var string Exception debug backtrace string
     */
    public $trace;
}
