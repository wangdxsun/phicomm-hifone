<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/20
 * Time: 16:16
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\LikeBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;

class LikeController extends AppController
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