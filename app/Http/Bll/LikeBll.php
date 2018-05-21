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
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class LikeBll extends BaseBll
{
    public function likeThread($thread)
    {
        if ($thread->status <> Thread::VISIBLE) {
            throw new HifoneException('该帖子已被删除', 410);
        }
        dispatch(new AddLikeCommand($thread));

        return ['liked' => Auth::user()->hasLikeThread($thread)];
    }

    public function likeReply($reply)
    {
        if ($reply->status <> Reply::VISIBLE) {
            throw new HifoneException('该评论已被删除');
        }
        dispatch(new AddLikeCommand($reply));

        return ['liked' => Auth::user()->hasLikeReply($reply)];
    }

    public function likeQuestion($question)
    {
        if ($question->status <> Question::VISIBLE) {
            throw new HifoneException('该提问已被删除');
        }
        dispatch(new AddLikeCommand($question));

        return ['liked' => Auth::user()->hasLikeQuestion($question)];
    }

    public function likeAnswer($answer)
    {
        if ($answer->status <> Answer::VISIBLE) {
            throw new HifoneException('该回答已被删除');
        }
        dispatch(new AddLikeCommand($answer));

        return ['liked' => Auth::user()->hasLikeAnswer($answer)];
    }

    public function likeComment($comment)
    {
        if ($comment->status <> Comment::VISIBLE) {
            throw new HifoneException('该回复已被删除');
        }
        dispatch(new AddLikeCommand($comment));

        return ['liked' => Auth::user()->hasLikeComment($comment)];
    }
}