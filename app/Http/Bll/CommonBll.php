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
use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Events\User\UserWasLoggedinEvent;
use Input;

class CommonBll extends BaseBll
{
    /**
     * 触发用户每日签到
     */
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
}