<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\requests;

use shardimage\shardimagephpapi\base\exceptions\InvalidValueException;
use shardimage\shardimagephpapi\helpers\JsonHelper;
use shardimage\shardimagephpapi\web\Http;

/**
 * Restful request handler.
 */
class RestRequest extends BaseRequest
{

    /**
     * @var RestRequest body
     */
    protected $body;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function buildUri()
    {
        return isset($this->request->restId) ? '/' . $this->request->restId : '';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getBody()
    {
        return $this->getBodyOrGenerate();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getHeaders()
    {
        $headers = parent::getHeaders();
        $body = $this->getBody();
        if ($body) {
            $headers[Http::HEADER_CONTENT_LENGTH] = strlen($body);
        }
        return $headers;
    }

    /**
     * To avoid multiple calling, checks the body in property, if null, then generates, stores and returns with it.
     * 
     * @return string
     */
    protected function getBodyOrGenerate()
    {
        if (is_null($this->body)) {
            $body = null;
            if (!empty($this->request->postParams)) {
                $body = $this->service->useMsgPack ? msgpack_pack($this->request->safePostParams) : JsonHelper::encode($this->request->safePostParams);
                if ($body === false) {
                    throw new InvalidValueException('Invalid model value (must be UTF-8 compatible)!');
                }
            }

            $this->body = $body;
        }

        return $this->body;
    }

}
