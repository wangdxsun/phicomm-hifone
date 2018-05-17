<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/6
 * Time: 15:04
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\LikeBll;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;

class LikeController extends ApiController
{
    public function thread(Thread $thread, LikeBll $likeBll)
    {
        return $likeBll->likeThread($thread);
    }

    public function reply(Reply $reply, LikeBll $likeBll)
    {
        return $likeBll->likeReply($reply);
    }

    public function question(Question $question, LikeBll $likeBll)
    {
        return $likeBll->likeQuestion($question);
    }

    public function answer(Answer $answer, LikeBll $likeBll)
    {
        return $likeBll->likeAnswer($answer);
    }

    public function comment(Comment $comment, LikeBll $likeBll)
    {
        return $likeBll->likeComment($comment);
    }
}