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
        $answerBll->checkPermission();
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

    public function search($keyword, AnswerBll $answerBll)
    {
        if (empty($keyword)) {
            $answers = new LengthAwarePaginator([], 0, 15);
        } else {
            $answers = $answerBll->search($keyword);
        }

        return $answers;
    }

    public function pin(Answer $answer)
    {
        //1.取消置顶
        if (1 == $answer->order) {
            $answer->update(['order' => 0]);
            $this->updateOpLog($answer, '取消置顶回答');
        } else {
            $answer->update(['order' => 1]);
            $this->updateOpLog($answer, '置顶回答');
            event(new PinWasAddedEvent($answer->user, $answer));
        }
        return ['pin' => $answer->order > 0 ? true : false];
    }
}