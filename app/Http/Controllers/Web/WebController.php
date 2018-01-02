<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Controllers\Controller;

class WebController extends Controller
{
    /**
     * The HTTP response headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * The HTTP response meta data.
     *
     * @var array
     */
    protected $meta = [];

    /**
     * The HTTP response data.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * The HTTP response status code.
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Set the response headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    protected function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    protected function setMsg($msg)
    {
        $this->msg = $msg;
    }

    /**
     * Set the response meta data.
     *
     * @param array $meta
     *
     * @return $this
     */
    protected function setMetaData(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }


}
