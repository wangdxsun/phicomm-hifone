<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\AnswerBll;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;

class AnswerController extends WebController
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
}