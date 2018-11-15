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
 * API response object.
 */
class Response extends BaseObject implements \JsonSerializable
{
    use \shardimage\shardimagephpapi\base\SimpleJsonSerializerTrait;
    /**
     * @var string Unique content ID
     */
    public $id;

    /**
     * @var bool Is the request successful?
     */
    public $success = true;

    /**
     * @var ResponseError|null Error object for the request
     */
    public $error;

    /**
     * @var string|null Job ID for a polling request
     */
    public $jobId;

    /**
     * @var mixed General response data
     */
    public $data;

    /**
     * @var array Meta parameters in the response
     */
    public $meta = [];

    /**
     * @var callable Method to run after a successful non-polling request
     */
    public $callback;
}
