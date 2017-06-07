<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:06
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Events\User\UserWasLoggedinEvent;
use Input;

class CommonBll extends BaseBll
{
    public function login()
    {
        if (Auth::check()) {
            $activeDate = app('session')->get('active_date');

            if (!$activeDate || $activeDate != date('Ymd')) {
                event(new UserWasLoggedinEvent(Auth::user()));
                app('session')->put('active_date', date('Ymd'));
            }
        }
    }

    public function upload()
    {
        if (Input::has('image')) {
            $image = Input::get('image');
            $upload = dispatch(new UploadBase64ImageCommand($image));

            return $upload;
        } else {
            throw new \Exception('没有上传图片');
        }
    }
}