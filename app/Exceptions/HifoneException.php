<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/11/24
 * Time: 14:21
 */

namespace Hifone\Exceptions;

use Exception;
use Throwable;

class HifoneException extends Exception
{
    public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}