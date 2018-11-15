<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web;

use shardimage\shardimagephpapi\base\BaseObject;
use shardimage\shardimagephpapi\web\parsers\Header;

/**
 * File reference object.
 */
class FileReference extends BaseObject
{
    /**
     * @var string Referenced field
     */
    public $field;

    /**
     * @var string Referenced field ID (if array)
     */
    public $id;

    /**
     * @var mixed File
     */
    public $file;

    /**
     * Creates a HTTP header from the reference.
     *
     * @return string
     */
    public function createHeader()
    {
        $header = $this->field;
        if (isset($this->id)) {
            $header .= ';id="'.$this->id.'"';
        }

        return $header;
    }

    /**
     * Creates a reference from a HTTP header.
     *
     * @param string $header Header in string
     * @param mixed  $file
     *
     * @return \self
     */
    public static function fromHeader($header, $file)
    {
        $header = new Header($header);

        return new self([
            'field' => $header->value,
            'id' => $header->getParam('id'),
            'file' => $file,
        ]);
    }
}
