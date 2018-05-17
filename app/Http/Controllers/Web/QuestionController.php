<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:21
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\QuestionBll;
use Hifone\Models\Question;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $tagIds = $questionBll->getValidTagIds(request('tag_ids'));
        if (mb_strlen(strip_tags(request('body'))) > 800) {
            throw new HifoneException('请输入内容0~800个字');
        } elseif (count($tagIds) == 0) {
            throw new HifoneException('请选择标签哦~');
        } elseif (count($tagIds) > 4) {
            throw new HifoneException('最多选择4个标签哦~');
        }
        $questionBll->checkScore(Auth::user()->phicomm_id);

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

    //获取悬赏梯度
    public function rewards()
    {
        $rewards = explode(',', env('REWARDS') ? : '5,10,15,20');

        return ['rewards' => $rewards];
    }

    public function search($keyword, QuestionBll $questionBll)
    {
        if (empty($keyword)) {
            $questions = new LengthAwarePaginator([], 0, 15);
        } else {
            $questions = $questionBll->search($keyword);
        }

        return $questions;
    }

    public function pin(QuestionBll $questionBll, Question $question)
    {
        return $questionBll->pin($question);
    }

    public function setExcellent(QuestionBll $questionBll, Question $question)
    {
        return $questionBll->setExcellent($question);
    }

}