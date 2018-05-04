<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:37
 */

namespace Hifone\Http\Bll;

use Hifone\Models\Question;
use Auth;
use Hifone\Models\User;

class QuestionBll extends BaseBll
{
    public function questions($tagId)
    {
        if ($tagId) {
            $questions = Question::with(['user', 'tags'])->ofTag($tagId)->recent()->paginate();
        } else {
            $questions = Question::with(['user', 'tags'])->orderBy('order', 'desc')->recent()->paginate();
        }

        return $questions;
    }

    public function showQuestion(Question $question)
    {
        $question = $question->load(['user', 'tags']);
        $question->followed = Auth::check() ? Auth::user()->hasFollowQuestion($question) : false;
        $question->user->followed = Auth::check()? User::hasFollowUser($question->user) : false;
        $question->reported = Auth::check() ? Auth::user()->hasReportQuestion($question) : false;

        return $question;
    }
}