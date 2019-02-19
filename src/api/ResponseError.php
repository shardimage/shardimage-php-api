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
use shardimage\shardimagephpapi\web\exceptions\HttpException;

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
     * HTTP response error.
     */
    const ERRORCODE_HTTP_RESPONSE_ERROR = 1004;

    /**
     * @var string Exception type
     */
    public $type;

    /**
     * @var int Exception code
     */
    public $code;

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

    /**
     * @var Exception Exception object itself
     */
    private $exception = false;

    /**
     * @var Response
     */
    private $response;
        
    /**
     * @var array Error message
     */
    private $message;

    /**
     * @return array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string|array $message
     */
    public function setMessage($message)
    {
        if (!is_array($message)) {
            $message = ['sdkError' => $message];
        }
        $this->message = $message;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        if ($this->exception === false) {
            $responseMessage = $this->message ?? null;
            $message = 'API error';
            if (is_string($responseMessage)) {
                $message = $responseMessage;
            } elseif (is_array($responseMessage)) {
                foreach ($responseMessage as $attribute => $errors) {
                    $errorMessage = $errors[0] ?? '';
                    $params = $errors[1] ?? [];
                    $placeholders = [];
                    foreach ((array) $params as $name => $value) {
                        $placeholders['{' . $name . '}'] = $value;
                    }
                    $message .= sprintf(' / %s: %s', $attribute, ($placeholders === []) ? $errorMessage : strtr($errorMessage, $placeholders));
                }
            }
            $this->exception = HttpException::newInstance($this->response->meta['statusCode'], $message, $this->code, is_array($responseMessage) ? $responseMessage : null);
        }
        return $this->exception;
    }

    /**
     * {@inheritdoc}
     */
    protected function getToArrayAttributes()
    {
        return array_merge(parent::getToArrayAttributes(), ['message']);
    }

}
