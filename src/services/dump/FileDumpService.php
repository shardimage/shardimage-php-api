<?php

/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\services\dump;

use shardimage\shardimagephpapi\base\BaseObject;
use shardimage\shardimagephpapi\base\exceptions\InvalidConfigException;

/**
 * FileDumpService, dumping request and response data and exception details to file.
 */
class FileDumpService extends BaseObject implements DumpServiceInterface
{

    /**
     * @var string path to save the dump 
     */
    public $dumpHttpCommunicationPath;

    /**
     * @var int maximal limit of the dumped file 
     */
    public $dumpHttpCommunicationSizeLimit = 256 * 1024;

    /**
     * @var string file name for the dumped request data 
     */
    public $dumpHttpCommunicationRequestFileName = 'send.dump.http';

    /**
     * @var string file name for the dumped response data 
     */
    public $dumpHttpCommunicationResponseFileName = 'receive.dump.http';

    /**
     * @var string file name for the exception data 
     */
    public $dumpHttpCommunicationExceptionFileName = 'error.txt';

    /**
     * @var string content ID for filename prefix
     */
    private $contentId;

    /**
     * {@inheritDoc}
     */
    protected function init()
    {
        parent::init();
        if (!isset($this->dumpHttpCommunicationPath)) {
            throw new InvalidConfigException(__CLASS__ . ': dumpHttpCommunicationPath property must be set!');
        }
    }

    /**
     * Saving data to file
     * @param string $data
     * @param string $type
     * @return boolean
     */
    public function save($data, $type)
    {
        if (!isset($data) || !isset($this->dumpHttpCommunicationPath) || !isset($this->contentId)) {
            return false;
        }
        $fileName = '';
        switch ($type) {
            case static::DUMPTYPE_REQUEST:
                $fileName = sprintf('%s/%s.%s', $this->dumpHttpCommunicationPath, $this->contentId, $this->dumpHttpCommunicationRequestFileName);
                break;
            case static::DUMPTYPE_RESPONSE:
                $fileName = sprintf('%s/%s.%s', $this->dumpHttpCommunicationPath, $this->contentId, $this->dumpHttpCommunicationResponseFileName);
                break;
            default:
                return false;
        }
        return $this->checkAndSaveFile($fileName, $data);
    }

    /**
     * Saving the exception details to file
     * @param /Exception $exception
     */
    public function saveException($exception)
    {
        if (!($exception instanceof \Exception) || !isset($this->dumpHttpCommunicationPath) || !isset($this->contentId)) {
            return false;
        }
        $fileName = sprintf('%s/%s.%s', $this->dumpHttpCommunicationPath, $this->contentId, $this->dumpHttpCommunicationExceptionFileName);
        $exceptionData = sprintf("%s: %s in %s:%s\nStack trace:\n%s", get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
        return $this->checkAndSaveFile($fileName, $exceptionData);
    }

    /**
     * Setting up the content ID as the prefix
     * @param string $value
     */
    public function setPrefix($value)
    {
        $this->contentId = $value;
    }

    /**
     * Validating and saving logics
     * @param string $filePath
     * @param string $data
     * @return boolean
     */
    private function checkAndSaveFile($filePath, $data)
    {
        if (file_exists($filePath) || mb_strlen($data) > $this->dumpHttpCommunicationSizeLimit) {
            return false;
        }
        return file_put_contents($filePath, $data) !== false;
    }

}
