<?php
/**
 * @link https://github.com/shardimage/shardimage-php-api
 * @link https://developers.shardimage.com
 *
 * @copyright Copyright (c) 2018 Shardimage
 * @license https://github.com/shardimage/shardimage-php-api/blob/master/LICENCE.md
 */

namespace shardimage\shardimagephpapi\web\requests;

/**
 * Polling request handler.
 */
class PollRequest extends BaseRequest
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function buildUri()
    {
        return '!/poll/'.$this->request->pollId;
    }
}
