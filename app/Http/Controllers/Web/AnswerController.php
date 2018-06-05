<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\AnswerBll;
use Hifone\Http\Bll\CommentBll;
use Hifone\Models\Answer;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;

class AnswerController extends WebController
{
    public function index()
    {

    }

    public function store(AnswerBll $answerBll)
    {
        $answerBll->checkPermission(Auth::user());
        $answerBll->checkQuestion(request('question_id'));

        $wordCount = mb_strlen(strip_tags(request('body')));
        if ($wordCount < 5 || $wordCount > 800) {
            throw new HifoneException('请输入内容5~800个字');
        }

        $answerData = [
            'body' => request('body'),
            'question_id' => request('question_id')
        ];
        $answer = $answerBll->createAnswer($answerData);

        return $answer;
    }

    public function show(Answer $answer, AnswerBll $answerBll)
    {
        return $answerBll->showAnswer($answer);
    }

    public function comments(Answer $answer, AnswerBll $answerBll, CommentBll $commentBll)
    {
        $answerBll->checkQuestion($answer->question_id);
        $commentBll->checkAnswer($answer->id);

        return $answerBll->sortComments($answer);
    }

    public function search($keyword, AnswerBll $answerBll)
    {
        if (empty($keyword)) {
            $answers = new LengthAwarePaginator([], 0, 15);
        } else {
            $answers = $answerBll->search($keyword);
        }

        return $answers;
    }

    public function pin(Answer $answer, AnswerBll $answerBll)
    {
        return $answerBll->pin($answer);
    }
}