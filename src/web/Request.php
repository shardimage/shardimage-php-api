<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Uri as GuzzleUri;
use shardimage\shardimagephpapi\api\auth\BaseAuthData;
use shardimage\shardimagephpapi\api\Request as ApiRequest;
use shardimage\shardimagephpapi\api\Response as ApiResponse;
use shardimage\shardimagephpapi\api\ResponseError;
use shardimage\shardimagephpapi\base\exceptions\Exception;
use shardimage\shardimagephpapi\base\exceptions\InvalidCallException;
use shardimage\shardimagephpapi\base\exceptions\InvalidValueException;
use shardimage\shardimagephpapi\helpers\ApiHelper;
use shardimage\shardimagephpapi\helpers\FileHelper;
use shardimage\shardimagephpapi\helpers\JsonHelper;
use shardimage\shardimagephpapi\services\Client;
use shardimage\shardimagephpapi\web\parsers\RawResponse;
use shardimage\shardimagephpapi\web\requests\CustomRequest;
use shardimage\shardimagephpapi\web\requests\FileRequest;
use shardimage\shardimagephpapi\web\requests\MultipartRequest;
use shardimage\shardimagephpapi\web\requests\PollRequest;
use shardimage\shardimagephpapi\web\requests\RestRequest;
use shardimage\shardimagephpapi\web\exceptions\HttpException;

/**
 * Request handler object.
 */
class Request
{

    /**
     * @var Client Client service
     */
    private $client;

    /**
     * @var ApiRequest[] API requests
     */
    private $requests = [];

    /**
     * @var array Cached contents
     */
    private $cache = [];

    /**
     * Creates a request handler.
     *
     * @param Client                       $client  Client service
     * @param ApiRequest|ApiRequest[]|null $request API request(s)
     */
    public function __construct($client, $request = null)
    {
        $this->client = $client;

        if (isset($request)) {
            $this->add($request);
        }
    }

    /**
     * Adds one or more API requests to the request handler.
     *
     * @param ApiRequest|ApiRequest[] $requests API request(s)
     */
    public function add($requests)
    {
        if (!is_array($requests)) {
            $requests = [$requests];
        }

        $this->requests = array_merge($this->requests, $requests);
    }

    /**
     * Sends the request, then parses and returns the response.
     * If the backend has thrown a general exception, throws the exception here as well.
     *
     * @return ApiResponse|ApiResponse[]
     */
    public function send()
    {
        $request = $this->createRequest();

        $log = "--------------------------------\r\nRequest\r\n--------------------------------\r\n";
        $log .= (string) $request->getMethod() . ' ' . (string) $request->getUri() . "\r\n";
        foreach ($request->getHeaders() as $header => $values) {
            $log .= $header . ': ' . implode('; ', $values) . "\r\n";
        }
        $log .= (string) $request->getBody();
        $this->client->log(LOG_INFO, $log);

        $options = [
            'decode_content' => false,
            'timeout' => $this->client->timeout,
        ];
        if (isset($this->client->proxy)) {
            $options['proxy'] = $this->client->proxy;
        }
        $client = new \GuzzleHttp\Client($options);
        try {
            $clientResponse = $client->send($request);
        } catch (\GuzzleHttp\Exception\RequestException $ex) {
            $clientResponse = $ex->getResponse();
            if ($clientResponse === null) {
                throw new HttpException($ex->getMessage());
            }
        }

        $log = "--------------------------------\r\nResponse\r\n--------------------------------\r\n";
        $log .= $clientResponse->getStatusCode() . "\r\n";
        if ($clientResponse->getStatusCode() == 204) {
            $clientResponse = $clientResponse->withoutHeader('Content-Type');
        }
        foreach ($clientResponse->getHeaders() as $header => $values) {
            $log .= $header . ': ' . implode('; ', $values) . "\r\n";
        }
        $log .= (string) $clientResponse->getBody();
        $this->client->log(LOG_INFO, $log);

        $response = $this->parseResponse($clientResponse);
        $this->handleError($response, $clientResponse);

        return $response;
    }

    /**
     * Creates the main Guzzle request from the API requests.
     *
     * @return GuzzleRequest
     */
    private function createRequest()
    {
        $requests = $this->createGuzzleRequests();
        if (count($requests) == 1) {
            $request = reset($requests);
        } else {
            $multipartRequest = new MultipartRequest($this->client, new ApiRequest([
                        'version' => 'latest',
                        'requests' => $requests,
                        'boundary' => ApiHelper::generateId(),
                        'multipartType' => 'mixed',
            ]));
            $request = $this->createGuzzleRequest($multipartRequest->getMethod(), $multipartRequest->getUri(), $multipartRequest->getHeaders(), $multipartRequest->getBody());
        }

        return $request;
    }

    /**
     * Throws an exception if a general error occured on the backend.
     *
     * @param ApiResponse $response API response
     * @param mixed $clinetResponse
     * @throws \Exception
     */
    private function handleError($response, $clientResponse = null)
    {
        if ($response instanceof ApiResponse && !$response->success) {
            $error = $response->error;
            if (is_array($error->message)) {
                // detailed errors
                return;
            }
            switch ($error->code) {
                case ResponseError::ERRORCODE_VALIDATION_FAILURE:
                case ResponseError::ERRORCODE_OBJECT_NOT_FOUND:
                case ResponseError::ERRORCODE_HTTP_ERROR:
                    break;
                default:
                    $message = !empty($error->message) ? $error->message : 'Unknown exception';
                    $code = $error->code ?? 0;
                    $ex = false;
                    if (!empty($error->type)) {
                        $class = $error->type;
                        if (!is_a($class, HttpException::class, true)) {
                            if (isset($error->file)) {
                                $message .= ' in ' . $error->file . ':' . $error->line;
                            }
                            if (isset($error->trace)) {
                                $message .= "\n\nRemote stack trace:\n" . (is_array($error->trace) ? implode("\n", $error->trace) : $error->trace);
                            }
                            $ex = new $class($message, $code);
                        }
                    }
                    if (!$ex) {
                        $statusCode = 0;
                        if($response) {
                            $statusCode = $response->meta['statusCode'] ?? 0;
                        } elseif($clientResponse) {
                            $statusCode = $clientResponse->getStatusCode();
                        }
                        $headers = null;
                        if ($clientResponse) {
                            $headers = $clientResponse->getHeaders();
                        }
                        $ex = HttpException::newInstance($statusCode, $message, $code, null, null, $headers);
                    }
                    throw $ex;
            }
        }
    }

    /**
     * Creates the Guzzle request(s) for the API request(s).
     *
     * @return GuzzleRequest[]
     *
     * @throws InvalidCallException
     */
    private function createGuzzleRequests()
    {
        $guzzleRequests = [];
        if (empty($this->requests)) {
            throw new InvalidCallException('Empty API request!');
        }
        foreach ($this->requests as $request) {
            if ($request->isRestCompatible()) {
                $httpRequest = new RestRequest($this->client, $request);
            } elseif (isset($request->pollId)) {
                $httpRequest = new PollRequest($this->client, $request);
            } else {
                $httpRequest = new CustomRequest($this->client, $request);
            }
            $guzzleRequest = $this->createGuzzleRequest($httpRequest->getMethod(), $httpRequest->getUri(), $httpRequest->getHeaders(), $httpRequest->getBody());
            $modeHeader = $request->mode;
            if ($request->ttl) {
                $modeHeader .= ';ttl=' . $request->ttl;
            }
            if ($request->expire) {
                $modeHeader .= ';expire=' . $request->expire;
            }
            $guzzleRequest = $guzzleRequest->withHeader($this->client->buildCustomHeader('Mode'), $modeHeader);
            if (isset($request->notificationUrl)) {
                $guzzleRequest = $guzzleRequest->withHeader($this->client->buildCustomHeader('Notification-Url'), $request->notificationUrl);
            }
            if (isset($request->userAgent)) {
                if (substr($request->userAgent, 0, 1) == '!') {
                    $userAgent = substr($request->userAgent, 1);
                } else {
                    $config = (new \GuzzleHttp\Client())->getConfig();
                    $userAgent = isset($config['headers']['User-Agent']) ? $config['headers']['User-Agent'] . ' ' . $request->userAgent : $request->userAgent;
                }
                $guzzleRequest = $guzzleRequest->withHeader('User-Agent', $userAgent);
            }
            $this->createFiles($request);
            if (empty($request->files)) {
                $guzzleRequests[] = $guzzleRequest;
            } else {
                $multipartRequest = $this->createMultipartRelatedRequest($request, $guzzleRequest);
                $guzzleRequests[] = $this->createGuzzleRequest($multipartRequest->getMethod(), $multipartRequest->getUri(), $multipartRequest->getHeaders(), $multipartRequest->getBody());
            }
        }

        return $guzzleRequests;
    }

    /**
     * Creates a multipart/related request for an API request with files.
     *
     * @param ApiRequest    $request       API request
     * @param GuzzleRequest $guzzleRequest Guzzle request
     *
     * @return MultipartRequest
     */
    private function createMultipartRelatedRequest($request, $guzzleRequest)
    {
        $requests = [$guzzleRequest];
        foreach ($request->files as $fileReference) {
            $fileRequest = new FileRequest($this->client, new ApiRequest([
                        'version' => $request->version,
                        'refId' => $request->id,
                        'refAttribute' => $fileReference->createHeader(),
                        'file' => $fileReference->file,
            ]));
            $requests[] = $this->createGuzzleRequest($fileRequest->getMethod(), $fileRequest->getUri(), $fileRequest->getHeaders(), $fileRequest->getBody());
        }

        return new MultipartRequest($this->client, new ApiRequest([
                    'requests' => $requests,
                    'boundary' => ApiHelper::generateId(),
                    'multipartType' => 'related',
                    'mode' => $request->mode,
        ]));
    }

    /**
     * Parses the response and returns one or more API responses.
     *
     * @param string|\Psr\Http\Message\ResponseInterface $response Response
     *
     * @return ApiResponse|ApiResponse[]
     */
    private function parseResponse($response)
    {
        $rawResponse = new RawResponse($this->client, $response);
        $contentId = isset($rawResponse->headers['content-id']) ? $rawResponse->headers['content-id'] : null;
        if (!isset($contentId) && count($this->cache) == 1) {
            $contentId = key($this->cache);
        }
        if ($this->client->cache && isset($contentId)) {
            if (isset($this->cache[$contentId])) {
                if ($rawResponse->statusCode == Http::STATUS_NOT_MODIFIED) {
                    $response = $this->cache[$contentId]->getContent();
                    $rawResponse = new RawResponse($this->client, $response);
                    $rawResponse->headers['content-id'] = $contentId;
                    $log = "--------------------------------\r\nCached response\r\n--------------------------------\r\n";
                    $log .= $rawResponse->statusCode . "\r\n";
                    foreach ($rawResponse->headers as $header => $value) {
                        $log .= $header . ': ' . $value . "\r\n";
                    }
                    $log .= $rawResponse->body;
                    $this->client->log(LOG_INFO, $log);
                } else {
                    $this->cache[$contentId]->delete();
                }
            }
            if ($rawResponse->statusCode != Http::STATUS_NOT_MODIFIED && $rawResponse->statusCode < Http::STATUS_INTERNAL_SERVER_ERROR && isset($rawResponse->headers['etag'])) {
                $this->cache[$contentId]->set($rawResponse->headers['etag'], $rawResponse->message);
            }
        }
        $method = 'parse' . ApiHelper::camelize($rawResponse->type) . 'response';
        if (!method_exists($this, $method)) {
            $method = 'parseDefaultResponse';
        }

        return $this->$method($rawResponse);
    }

    /**
     * Parses a multipart/mixed response and returns API responses.
     *
     * @param RawResponse $rawResponse Raw HTTP response
     *
     * @return ApiResponse[]
     */
    private function parseMultipartMixedResponse($rawResponse)
    {
        $responses = [];
        foreach ($rawResponse->parts as $part) {
            $response = $this->parseResponse($part);
            $responses[$response->id] = $response;
        }

        return $responses;
    }

    /**
     * Parses a multipart/related response and returns API responses.
     *
     * @param RawResponse $rawResponse Raw HTTP response
     *
     * @return ApiResponse[]
     */
    private function parseMultipartRelatedResponse($rawResponse)
    {
        $responseIds = [];
        foreach ($rawResponse->parts as $id => $part) {
            $response = new RawResponse($this->client, $part);
            if ($response->isFile()) {
                $this->parseResponse($part);
            } else {
                $responseIds[] = $id;
            }
        }
        $responses = [];
        foreach ($responseIds as $id) {
            $responses[] = $this->parseResponse($rawResponse->parts[$id]);
        }

        return $responses;
    }

    /**
     * Processes an application/octet-stream response for files.
     *
     * @param RawResponse $rawResponse Raw HTTP response
     */
    private function parseApplicationOctetStreamResponse($rawResponse)
    {
        $filename = sys_get_temp_dir() . '/' . ApiHelper::generateId() . '.tmp';
        if (file_put_contents($filename, $rawResponse->body)) {
            $preferredResource = $this->client->preferredResource;
            $resource = $preferredResource::fromResource(fopen($filename, 'r'), $rawResponse->customHeaders['filename']);
            FileHelper::addFile($rawResponse->customHeaders['ref-id'], $rawResponse->customHeaders['ref-attribute'], $resource);
        }
    }

    /**
     * Parses a standard response and returns an API response.
     *
     * @param RawResponse $rawResponse Raw HTTP response
     *
     * @return ApiResponse
     */
    private function parseDefaultResponse($rawResponse)
    {
        if (isset($rawResponse->body) && $rawResponse->body != '') {
            switch ($rawResponse->type) {
                case 'application/json':
                    $content = JsonHelper::decode((string) $rawResponse->body);
                    break;
                case 'application/msgpack':
                case 'application/x-msgpack':
                    if (function_exists('msgpack_pack')) {
                        $content = msgpack_unpack((string) $rawResponse->body);
                    } else {
                        throw new InvalidValueException('MsgPack PHP extension not installed!');
                    }
                    break;
                default:
                    $body = "BinaryContent";
                    if (mb_detect_encoding($rawResponse->body, 'UTF-8')) {
                        $body = $rawResponse->body;
                        if (mb_strlen($body) > 500) {
                            $body = mb_substr($body, 0, 500) . "...";
                        }
                    }
                    $message = sprintf('Unsupported content type in response! Type:%s Body: %s', $rawResponse->type, $body);
                    throw new InvalidValueException($message);
            }
            if (!isset($content)) {
                throw new InvalidValueException('Invalid content in response!');
            }
        } else {
            $content = null;
        }
        if (!isset($content['success'])) {
            $content = [
                'success' => true,
                'data' => $content,
            ];
        }
        if (!isset($rawResponse->headers['content-id']) && count($this->requests) == 1) {
            $rawResponse->headers['content-id'] = $this->requests[0]->id;
        }
        if (!isset($rawResponse->headers['content-id'])) {
            throw new Exception('Invalid response!');
        }
        $content['id'] = $rawResponse->headers['content-id'];
        $meta = isset($content['meta']) ? $content['meta'] : [];
        $content['meta'] = array_merge($rawResponse->customHeaders, $meta);
        $content['meta']['statusCode'] = (int) $rawResponse->statusCode;
        if (isset($content['data'])) {
            FileHelper::processFiles($content['data'], $content['id']);
        }
        if ((int) $rawResponse->statusCode >= 400 && $content['success']) {
            $content['success'] = false;
            $content['error'] = [
                'code' => ResponseError::ERRORCODE_HTTP_ERROR,
                'message' => $rawResponse->statusCode,
            ];
        }

        return new ApiResponse($content);
    }

    /**
     * Creates a Guzzle request.
     *
     * @param string $method  HTTP method
     * @param string $uri     Uri
     * @param array  $headers Headers
     * @param mixed  $body    Body
     *
     * @return GuzzleRequest
     */
    private function createGuzzleRequest($method, $uri, $headers, $body)
    {
        $uri = (string) new GuzzleUri($uri);
        if ($this->client->authData instanceof BaseAuthData) {
            $this->client->authData->method = $method;
            $this->client->authData->uri = $uri;
            $authData = $this->client->authData;
        } else {
            $authData = new $this->client->authData([
                'method' => $method,
                'uri' => $uri,
            ]);
        }
        foreach ($authData->credentials() as $key => $value) {
            $value = is_callable($value) ? call_user_func($value) : $value;
            if (isset($value)) {
                $headers[$this->client->buildCustomHeader('Auth-' . $key)] = $value;
            }
        }
        foreach ($this->client->customHeaders as $key => $value) {
            $value = is_callable($value) ? call_user_func($value) : $value;
            if (isset($value)) {
                $headers[$this->client->buildCustomHeader($key)] = $value;
            }
        }
        foreach ($this->client->headers as $key => $value) {
            $value = is_callable($value) ? call_user_func($value) : $value;
            if (isset($value)) {
                $headers[$key] = $value;
            }
        }
        if (isset($body)) {
            $headers[Http::HEADER_EXPECT] = '100-continue';
        }
        if ($this->client->useGzip) {
            $headers[Http::HEADER_ACCEPT_ENCODING] = 'gzip';
        }
        if ($this->client->useMsgPack) {
            $headers[Http::HEADER_ACCEPT] = 'application/json,application/msgpack';
        }
        if (!isset($headers[Http::HEADER_CONTENT_ID])) {
            $headers[Http::HEADER_CONTENT_ID] = ApiHelper::generateId();
        }
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $headers[Http::HEADER_ACCEPT_LANGUAGE] = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        if ($this->client->cache && in_array($method, ['GET', 'HEAD'])) {
            $cachedContent = $this->cache[$headers[Http::HEADER_CONTENT_ID]] = new CachedContent([
                'client' => $this->client,
                'method' => $method,
                'uri' => $uri,
            ]);
            if ($eTag = $cachedContent->getETag()) {
                $headers[Http::HEADER_IF_NONE_MATCH] = $eTag;
            }
        }
        ksort($headers);

        return new GuzzleRequest($method, $this->client->host . $uri, $headers, $body);
    }

    /**
     * Processes the files in an API request.
     *
     * @param ApiRequest $request API request
     */
    private function createFiles($request)
    {
        $this->createFilesFromArray($request, $request->getParams);
        $this->createFilesFromArray($request, $request->postParams);
    }

    /**
     * Processes the files in an array inserting them into an API request.
     *
     * @param ApiRequest $request API request
     * @param array      $array   Array
     * @param string     $type    File reference type
     * @param string     $name    File reference model name
     */
    private function createFilesFromArray($request, $array)
    {
        foreach ($array as $key => $value) {
            if ($file = $this->client->getResourceHandler($value)) {
                $request->files[] = new FileReference([
                    'field' => $key,
                    'file' => $file,
                ]);
            } elseif (is_array($value)) {
                foreach ($value as $id => $_value) {
                    if ($file = $this->client->getResourceHandler($_value)) {
                        $request->files[] = new FileReference([
                            'field' => $key,
                            'id' => $id,
                            'file' => $file,
                        ]);
                    }
                }
            }
        }
    }

}
