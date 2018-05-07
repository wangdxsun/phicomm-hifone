<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:21
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\QuestionBll;
use Hifone\Models\Question;
use Auth;

class QuestionController extends WebController
{
    public function index(QuestionBll $questionBll)
    {
        $tagId = request('tag_id');
        $questions = $questionBll->questions($tagId);

        return $questions;
    }

    public function excellent()
    {
        $questions = Question::with(['user', 'tags'])->orderBy('is_excellent', 'desc')->orderBy('order', 'desc')->recent()->limit(3)->get();

        return $questions;
    }

    public function store(QuestionBll $questionBll)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $this->validate(request(), [
            'title' => 'required|min:5|max:40',
            'score'=> 'required|int|min:5'
        ]);
        $tagIds = explode(',', request('tag_ids'));
        if (mb_strlen(strip_tags(request('body'))) > 800) {
            throw new HifoneException('请输入内容0~800个字');
        } elseif (count($tagIds) == 0 || count($tagIds) > 4) {
            throw new HifoneException('请选择0~4个分类');
        }
        //todo 判断智慧果是否够用
        $questionData = [
            'title' => request('title'),
            'body' => request('body'),
            'score' => request('score'),
            'tagIds' => $tagIds
        ];
        $question = $questionBll->createQuestion($questionData);

        return $question;
    }

    public function show(Question $question, QuestionBll $questionBll)
    {
        $question = $questionBll->showQuestion($question);

        return $question;
    }
}