<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:48
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Http\Bll\UserBll;

class CommonController extends ApiController
{
    public function upload(CommonBll $commonBll)
    {
        return $commonBll->upload();
    }

    public function uploadBase64(CommonBll $commonBll)
    {
        return $commonBll->uploadBase64();
    }
}