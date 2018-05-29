<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:04
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\CommentBll;
use Auth;

class CommentController extends WebController
{
    public function store(CommentBll $commentBll)
    {
        $commentBll->checkPermission(Auth::user());
        $commentBll->checkComment(request('comment_id'));
        $commentBll->checkAnswer(request('answer_id'));

        $commentData = [
            'body' => request('body'),
            'answer_id' => request('answer_id'),
            'comment_id' => request('comment_id'),
        ];
        $comment = $commentBll->createComment($commentData);

        return $comment;
    }
}