<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\api;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use shardimage\shardimagephpapi\base\BaseObject;
use shardimage\shardimagephpapi\helpers\ApiHelper;
use shardimage\shardimagephpapi\web\FileReference;

/**
 * API request object.
 * 
 * @property array $safeParams Safe mandatory parameters
 * @property array $safeGetParams Safe GET parameters
 * @property array $safePostParams Safe POST parameters
 */
class Request extends BaseObject
{
    /**
     * Synchronous request.
     */
    const MODE_SYNC = 'sync/serial';

    /**
     * Asynchronous request.
     */
    const MODE_ASYNC = 'async/serial';

    /**
     * Parallel synchronous request.
     */
    const MODE_SYNC_PARALLEL = 'sync/parallel';

    /**
     * Parallel asynchronous request.
     */
    const MODE_ASYNC_PARALLEL = 'async/parallel';

    /**
     * @var string Synchronous/asynchronous request
     */
    public $mode = self::MODE_SYNC_PARALLEL;

    /**
     * @var int Max. execution time of the request on the backend (secs, 0: no limit)
     */
    public $ttl = 0;

    /**
     * @var int Expire time of the request result on the backend (secs, 0: no limit)
     */
    public $expire = 0;

    /**
     * @var string API version
     */
    public $version;

    /**
     * @var string API module
     */
    public $module;

    /**
     * @var string API controller
     */
    public $controller;

    /**
     * @var string Restful action
     */
    public $restAction;

    /**
     * @var mixed Restful ID
     */
    public $restId;

    /**
     * @var string Non-restful (custom) action
     */
    public $customAction;

    /**
     * @var array Mandatory parameters
     */
    public $params = [];

    /**
     * @var array GET parameters
     */
    public $getParams = [];

    /**
     * @var array POST parameters
     */
    public $postParams = [];
    /**
     *
     * @var callable Optional URI parameter formatter function
     *
     * function($param, $value) {
     *     return $value;
     * }
     */
    public $uriParamFormatter;

    /**
     * @var FileReference[] File references
     */
    public $files = [];

    /**
     * @var string Polling request ID
     */
    public $pollId;

    /**
     * @var GuzzleRequest Subrequests of a multipart request (like files)
     */
    public $requests = [];

    /**
     * @var string HTTP boundary for a multipart request
     */
    public $boundary;

    /**
     * @var string HTTP user agent
     */
    public $userAgent;

    /**
     * @var string HTTP method
     */
    public $method = 'POST';

    /**
     * @var string Uri
     */
    public $uri;

    /**
     * @var string Unique content ID
     */
    public $id;

    /**
     * @var string Parent reference content ID for a subrequest
     */
    public $refId;

    /**
     * @var string Parent reference attribute for a subrequest (parameter or field of model)
     */
    public $refAttribute;

    /**
     * @var resource File resource for a file subrequest
     */
    public $file;

    /**
     * @var string Content type for a multipart request
     */
    public $multipartType;

    /**
     * @var string Optional notification URL
     */
    public $notificationUrl;

    /**
     * @var bool Is the request restful?
     */
    private $isRestCompatible;

    /**
     * @var bool[] Is the model required for a restful request?
     */
    private static $restRequiredModels = [
        'create' => true,
        'update' => true,
        'delete' => false,
        'view' => false,
        'index' => false,
        'exists' => false,
    ];

    /**
     * @var bool[] Is the "id" parameter mandatory for a restful request?
     */
    private static $restRequiredIds = [
        'create' => false,
        'update' => true,
        'delete' => true,
        'view' => true,
        'index' => false,
        'exists' => true,
    ];
    private static $restMethods = [
        'create' => 'POST',
        'update' => 'PUT',
        'delete' => 'DELETE',
        'view' => 'GET',
        'index' => 'GET',
        'exists' => 'HEAD',
    ];

    /**
     * Request initialization.
     * 
     * This method generates the content ID and determines the HTTP method.
     */
    public function init()
    {
        if (!isset($this->id)) {
            $this->id = ApiHelper::generateId();
        }
        if (isset($this->restAction, self::$restMethods[$this->restAction])) {
            $this->method = self::$restMethods[$this->restAction];
        }
    }

    /**
     * Returns whether the request is restful compatible.
     *
     * A request can be restful, if the action is restful and the existence of a model and the "id" parameter complies with that action.
     *
     * @return bool
     */
    public function isRestCompatible()
    {
        if (!isset($this->isRestCompatible)) {
            $this->isRestCompatible = $this->isRestAction() && $this->hasRestRequiredModel() && $this->hasRestRequiredId();
        }

        return $this->isRestCompatible;
    }

    /**
     * Returns the parameters after replacing the unsafe attributes with NULL (e.g. resources, objects).
     *
     * @return array
     */
    public function getSafeParams()
    {
        return ApiHelper::maskUnsafeAttributes($this->params);
    }

    /**
     * Returns the GET parameters after replacing the unsafe attributes with NULL (e.g. resources, objects).
     *
     * @return array
     */
    public function getSafeGetParams()
    {
        return ApiHelper::maskUnsafeAttributes($this->getParams);
    }

    /**
     * Returns the POST parameters after replacing the unsafe attributes with NULL (e.g. resources, objects).
     *
     * @return array
     */
    public function getSafePostParams()
    {
        return ApiHelper::maskUnsafeAttributes($this->postParams);
    }

    /**
     * Returns whether the request action is restful.
     * 
     * @return bool
     */
    private function isRestAction()
    {
        return in_array($this->restAction, ['create', 'update', 'view', 'delete', 'index', 'exists']);
    }

    /**
     * Returns whether a model is required for the restful action.
     * 
     * @return bool
     */
    private function hasRestRequiredModel()
    {
        return self::$restRequiredModels[$this->restAction] === !empty($this->postParams);
    }

    /**
     * Returns whether the "id" parameter is required for the restful action.
     * 
     * @return bool
     */
    private function hasRestRequiredId()
    {
        return self::$restRequiredIds[$this->restAction] === isset($this->restId);
    }
}
