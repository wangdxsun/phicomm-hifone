<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/1/2
 * Time: 16:04
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Http\Bll\CommonBll;
use Input;

class CommonController extends WebController
{

    //上传图片文件
    public function upload(CommonBll $commonBll)
    {
        return $commonBll->upload();
    }

    //上传图片Base64编码
    public function uploadBase64(CommonBll $commonBll)
    {
        return $commonBll->uploadBase64();
    }
}