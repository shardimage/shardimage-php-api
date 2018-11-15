<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\helpers;

use shardimage\shardimagephpapi\services\BaseService;
use shardimage\shardimagephpapi\web\FileReference;

/**
 * Helper methods for file-related operations.
 */
class FileHelper
{
    /**
     * Local temporal storage for file references.
     *
     * @var FileReference[] File references
     */
    private static $files = [];

    /**
     * Returns the size of a file.
     *
     * @param string|resource $file File content or resource
     *
     * @return int
     */
    public static function getFilesize($file)
    {
        if (is_string($file)) {
            return strlen($file);
        }

        $size = null;
        $filename = self::getFilename($file);
        if (!$filename && is_resource($file)) {
            $s = fstat($file);
            if (is_array($s)) {
                $size = $s['size'];
            }
        } else {
            $size = filesize($filename);
        }

        return $size;
    }

    /**
     * Returns the filename of a file resource.
     *
     * @param resource $resource File resource
     *
     * @return string|null
     */
    public static function getFilename($resource)
    {
        try {
            $metadata = stream_get_meta_data($resource);

            return realpath($metadata['uri']);
        } catch (\Exception $ex) {
        }
    }

    /**
     * Return the mime type of a file.
     *
     * @param string|resource $file File content or resource
     *
     * @return string
     */
    public static function getMimeType($file)
    {
        $mimeType = 'application/octet-stream';

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if (is_string($file)) {
                $mimeType = finfo_buffer($finfo, $file);
            } elseif ($filename = self::getFilename($file)) {
                $mimeType = finfo_file($finfo, $filename);
            }
            finfo_close($finfo);
        }

        return $mimeType;
    }

    /**
     * Adds a file reference to the local storage.
     *
     * @param string $refId        Reference content ID
     * @param string $refAttribute Reference attribute (parameter or field of model)
     * @param mixed  $file         File
     */
    public static function addFile($refId, $refAttribute, $file)
    {
        self::$files[$refId][] = FileReference::fromHeader($refAttribute, $file);
    }

    /**
     * Returns all file references for a content ID.
     *
     * @param string $refId Reference content ID
     *
     * @return FileReference[]
     */
    public static function getFiles($refId)
    {
        return isset(self::$files[$refId]) ? self::$files[$refId] : [];
    }

    /**
     * Removes all file references for a content ID or globally.
     *
     * @param BaseService $service Service
     * @param string      $refId   Reference content ID
     */
    public static function deleteFiles($service, $refId = null)
    {
        foreach (self::$files as $_refId => $files) {
            if (isset($refId) && $_refId !== $refId) {
                continue;
            }
            foreach ($files as $fileReference) {
                $fileReference->file->delete();
            }
            unset(self::$files[$_refId]);
        }
    }

    /**
     * Reinserts all files into the referenced content.
     *
     * @param array  $array Content
     * @param string $refId Content ID
     */
    public static function processFiles(&$array, $refId)
    {
        if (is_array($array)) {
            foreach (self::getFiles($refId) as $fileReference) {
                if (array_key_exists($fileReference->field, $array)) {
                    if (isset($fileReference->id)) {
                        $array[$fileReference->field][$fileReference->id] = $fileReference->file->getContent();
                    } else {
                        $array[$fileReference->field] = $fileReference->file->getContent();
                    }
                }
            }
        }
    }
}
