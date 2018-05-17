<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\AnswerBll;
use Hifone\Http\Controllers\App\AppController;
use Auth;
use Hifone\Models\Answer;

class AnswerController extends AppController
{
    public function index()
    {

    }

    public function store(AnswerBll $answerBll)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('你已被禁言');
        } elseif (Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        //App图文混排
        $bodies = json_decode(request('body'), true);
        $content = $this->makeMixedContent($bodies);

        $wordCount = mb_strlen(strip_tags($content));
        if ($wordCount < 5 || $wordCount > 800) {
            throw new HifoneException('请输入内容5~800个字');
        }

        $answerData = [
            'body' => $content,
            'question_id' => request('question_id')
        ];
        $answer = $answerBll->createAnswer($answerData);

        return $answer;
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