<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/6
 * Time: 15:03
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Like\AddLikeCommand;
use Auth;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LikeBll extends BaseBll
{
    public function likeThread($thread)
    {
        if ($thread->status <> Thread::VISIBLE) {
            throw new NotFoundHttpException('该帖子已被删除');
        }
        dispatch(new AddLikeCommand($thread));

        return ['liked' => Auth::user()->hasLikeThread($thread)];
    }

    public function likeReply($reply)
    {
        if ($reply->status <> Reply::VISIBLE) {
            throw new NotFoundHttpException('该评论已被删除');
        }
        dispatch(new AddLikeCommand($reply));

        return ['liked' => Auth::user()->hasLikeReply($reply)];
    }
}