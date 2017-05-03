<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:20
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Reply\AddReplyCommand;
use Input;
use Auth;

class ReplyBll extends BaseBll
{
    public function createReply()
    {
        $replyData = Input::get('reply');
        dispatch(new AddReplyCommand(
            $replyData['body'],
            Auth::id(),
            $replyData['thread_id']
        ));
    }
}