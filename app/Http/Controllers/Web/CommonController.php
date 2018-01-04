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
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;

class CommonController extends WebController
{

    //上传图片文件
    public function upload()
    {
        if (!request()->hasFile('image')) {
            throw new HifoneException('没有上传图片');
        }
        $upload = dispatch(new UploadImageCommand(request()->file('image')));
        unset($upload['localFile']);

        return $upload;
    }

    //上传图片Base64编码
    public function uploadBase64()
    {
        if (!request()->hasFile('image')) {
            throw new HifoneException('没有上传图片');
        }
        $upload = dispatch(new UploadBase64ImageCommand(request()->file('image')));
        unset($upload['localFile']);

        return $upload;
    }
}