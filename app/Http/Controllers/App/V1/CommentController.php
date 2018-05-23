<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:04
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\AnswerBll;
use Hifone\Http\Bll\CommentBll;
use Hifone\Http\Controllers\App\AppController;

class CommentController extends AppController
{
    public function index()
    {

    }

    public function store(CommentBll $commentBll)
    {
        $commentBll->checkPermission();
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