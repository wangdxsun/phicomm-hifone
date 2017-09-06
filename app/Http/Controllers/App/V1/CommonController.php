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
    public function uploadBase64()
    {
        $images = [];
        if (Input::has('images')) {
            for ($i = 0; $i < count(Input::get('images')); $i++) {
                $image = Input::get('images')[$i];
                $upload = dispatch(new UploadBase64ImageCommand($image));
                $images[] = $upload["filename"];
            }
        }
        return $images;
    }

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