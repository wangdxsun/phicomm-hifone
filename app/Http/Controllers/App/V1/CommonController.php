<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 17:17
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Controllers\App\AppController;

class CommonController extends AppController
{
    //上传图片Base64编码
    public function uploadBase64(CommonBll $commonBll)
    {
        return $commonBll->uploadBase64();
    }

    //上传图片文件
    public function upload(CommonBll $commonBll)
    {
        return $commonBll->upload();
    }
}