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

    /**
     * @var ResponseError|null Error object for the request
     */
    private $error;

    /**
     * @return ResponseError|null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @param ResponseError|array|null $error
     */
    public function setError($error) {
        if (!($error instanceof ResponseError) && !is_null($error)) {
            $error = new ResponseError($error);
        }
        if (!is_null($error)) {
            $error->setResponse($this);
        }
        $this->error = $error;
    }

    /**
     * {@inheritdoc}
     */
    protected function getToArrayAttributes()
    {
        return array_merge(parent::getToArrayAttributes(), ['error']);
    }

}
