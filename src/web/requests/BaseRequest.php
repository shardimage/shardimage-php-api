<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\requests;

use shardimage\shardimagephpapi\api\Request as ApiRequest;
use shardimage\shardimagephpapi\base\exceptions\InvalidCallException;
use shardimage\shardimagephpapi\base\exceptions\InvalidValueException;
use shardimage\shardimagephpapi\helpers\JsonHelper;
use shardimage\shardimagephpapi\services\Client;
use shardimage\shardimagephpapi\services\Server;
use shardimage\shardimagephpapi\web\Http;
use shardimage\shardimagephpapi\base\BaseObject;

/**
 * Extendable request handler.
 */
class BaseRequest
{
    /**
     * @var ApiRequest API request object
     */
    protected $request;

    /**
     * @var Client|Server Service
     */
    protected $service;

    /**
     * Creates the request handler.
     *
     * @param Client|Server $service Service
     * @param ApiRequest    $request API request
     */
    public function __construct($service, $request)
    {
        $this->service = $service;
        $this->request = $request;
    }

    /**
     * Returns the HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->request->method;
    }

    /**
     * Returns the uri after replacing the parameters.
     *
     * @return string
     */
    public function getUri()
    {
        $uri = isset($this->request->uri) ? $this->request->uri : $this->buildUri();
        $urlParams = self::parseUrlParams($uri);
        $getParams = $this->request->safeGetParams;
        if (!empty($urlParams)) {
            $replace = [];
            foreach ($this->request->safePostParams as $param => $value) {
                if (is_scalar($value) && in_array($param, $urlParams)) {
                    $this->prepareUrlReplaceParams($replace, $param, $value);
                }
            }
            foreach ($getParams as $param => $value) {
                if (is_scalar($value) && in_array($param, $urlParams)) {
                    $this->prepareUrlReplaceParams($replace, $param, $value);
                    unset($getParams[$param]);
                } elseif (is_array($value) || $value instanceof BaseObject) {
                    foreach ($value as $valueKey => $paramValue) {
                        $findKey = "$param.$valueKey";
                        if (in_array($findKey, $urlParams)) {
                            $this->prepareUrlReplaceParams($replace, $findKey, $paramValue);
                            unset($getParams[$param]);
                        }
                    }
                }
            }
            foreach ($this->request->safeParams as $param => $value) {
                if (is_scalar($value) && in_array($param, $urlParams)) {
                    $this->prepareUrlReplaceParams($replace, $param, $value);
                }
            }
            $uri = str_replace(array_keys($replace), $replace, $uri);
            $uri = preg_replace('#<[^>]+>#isu', '', $uri);
        }
        if (substr($uri, 0, 1) !== '!') {
            $uri = '/'.$this->request->module.'/'.$this->request->version.(isset($this->request->controller) ? '/'.$this->request->controller : '').$uri;
        } else {
            $uri = substr($uri, 1);
        }
        if (!empty($getParams)) {
            $uri .= '?'.http_build_query($getParams);
        }

        return $uri;
    }

    protected function formatUriParam($param, $value)
    {
        if (is_callable($this->request->uriParamFormatter)) {
            $value = call_user_func_array($this->request->uriParamFormatter, [
                'param' => $param,
                'value' => $value,
            ]);
        }

        return (string) $value;
    }

    protected static function parseUrlParams($uri)
    {
        preg_match_all('#<([^>]+)>#isu', $uri, $result);

        return $result[1];
    }

    /**
     * Prepares replace array
     * 
     * @param array $replace
     * @param string $findKey
     * @param string $paramValue
     */
    protected function prepareUrlReplaceParams(&$replace, $findKey, $paramValue)
    {
        $param = $this->formatUriParam($findKey, $paramValue);
        if (strlen($param) === 0) {
            throw new InvalidValueException(sprintf("Can't be empty string: %s", $findKey));
        }
        $replace['<' . $findKey . '>'] = $param;
    }

    /**
     * Builds the uri.
     *
     * @return string
     * 
     * @throws InvalidCallException
     */
    protected function buildUri()
    {
        throw new InvalidCallException(__METHOD__.' must be implemented!');
    }

    /**
     * Returns the headers in array.
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            Http::HEADER_CONTENT_TYPE => $this->service->useMsgPack ? 'application/msgpack' : 'application/json; charset=utf-8',
            Http::HEADER_CONTENT_ID => $this->request->id,
        ];
    }

    /**
     * Returns the body.
     *
     * @return string
     */
    public function getBody()
    {
        if (empty($this->request->postParams)) {
            return '';
        }
        $uri = isset($this->request->uri) ? $this->request->uri : $this->buildUri();
        $urlParams = self::parseUrlParams($uri);
        $postParams = array_diff_key($this->request->safePostParams, array_flip($urlParams));
        if (empty($postParams)) {
            return '';
        }
        $body = $this->service->useMsgPack ? msgpack_pack($postParams) : JsonHelper::encode($postParams);
        if ($body === false) {
            throw new InvalidValueException('Invalid body value (must be UTF-8 compatible)!');
        }

        return $body;
    }
}
