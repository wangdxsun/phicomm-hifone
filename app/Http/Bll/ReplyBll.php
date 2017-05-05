<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:20
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Commands\Reply\AddReplyCommand;
use Input;
use Auth;

class ReplyBll extends BaseBll
{
    public function createReply()
    {
        $replyData = Input::get('reply');
        //如果有单独上传图片，将图片拼接到正文后面
        if (Input::hasFile('images')) {
            foreach ($images = Input::file('images') as $image) {
                $res = dispatch(new UploadImageCommand($image));
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