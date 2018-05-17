<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\LikeBll;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class LikeController extends WebController
{
    public function thread(Thread $thread, LikeBll $likeBll)
    {
        return $likeBll->likeThread($thread);
    }

    public function reply(Reply $reply, LikeBll $likeBll)
    {
        return $likeBll->likeReply($reply);
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