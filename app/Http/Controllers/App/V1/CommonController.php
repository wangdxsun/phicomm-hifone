<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 17:17
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Http\Controllers\App\AppController;
use Input;

class CommonController extends AppController
{
    //上传图片Base64编码
    public function uploadBase64()
    {
        $images = [];
        if (Input::has('images')) {
            foreach (Input::get('images') as $image) {
                $upload = dispatch(new UploadBase64ImageCommand($image));
                $images[] = $upload["filename"];
            }
        }
        return $images;
    }

    //上传图片文件
    public function upload()
    {
        $images = [];
        if (Input::hasFile('images')) {
            $files = Input::file('images');
            foreach ($files as $image) {
                $upload = dispatch(new UploadImageCommand($image));
                $images[] = $upload["filename"];
            }
        }
        return $images;
    }
}