<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:20
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Reply\AddReplyCommand;
use Input;
use Auth;

class ReplyBll extends BaseBll
{
    public function createReply()
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('对不起，你已被管理员禁止发言');
        }
        $replyData = Input::get('reply');
        //如果有单独上传图片，将图片拼接到正文后面
        if (Input::has('images')) {
            $replyData['body'] = "<p>".$replyData['body']."</p>";
            foreach ($images = Input::get('images') as $image) {
                $res = dispatch(new UploadBase64ImageCommand($image));
                $replyData['body'] .= "<img src='{$res["filename"]}'/>";
            }
        }
        dispatch(new AddReplyCommand(
            $replyData['body'],
            Auth::id(),
            $replyData['thread_id']
        ));
    }
}