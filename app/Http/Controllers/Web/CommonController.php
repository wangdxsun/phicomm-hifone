<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/1/2
 * Time: 16:04
 */

namespace Hifone\Http\Controllers\Web;

use Gregwar\Captcha\CaptchaBuilder;
use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Exceptions\HifoneException;
use Session;

class CommonController extends WebController
{

    //上传图片文件
    public function upload()
    {
        if (!request()->hasFile('image')) {
            throw new HifoneException('没有上传图片');
        }
        $upload = dispatch(new UploadImageCommand(request()->file('image')));

        return $upload['filename'];
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

    public function captcha(CaptchaBuilder $builder)
    {
        $builder->build($width = 170, $height = 50, $font = null);

        Session::put('phrase', $builder->getPhrase());

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: image/jpeg');
        $builder->output();
    }
}