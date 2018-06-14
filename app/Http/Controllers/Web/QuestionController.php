<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:21
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\AnswerBll;
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
        $questions = Question::visible()->with(['user', 'tags'])->orderBy('is_excellent', 'desc')->orderBy('order', 'desc')->recent()->limit(3)->get();

        return $questions;
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

    public function store(QuestionBll $questionBll)
    {
        $questionBll->checkPermission(Auth::user());
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

    public function update(Question $question, QuestionBll $questionBll)
    {
        //修改问题标题，标签和内容
        $this->validate(request(),[
            'title'  =>     'min:5|max:40',
        ],[
            'title.min' => '标题5'. '-'.'40个字符',
            'title.max' => '标题5'. '-'.'40个字符',
        ]);
        $bodyLength = mb_strlen(strip_tags(request('body')));
        if ($bodyLength > 800) {
            throw new HifoneException('内容需0-800个字符');
        }
        $questionData = [
            'title' => request('title'),
            'body' => request('body'),
            'questionTags' => request('tag_ids')
        ];
        $body = app('parser.emotion')->reverseParseEmotionAndImage($questionData['body']);
        if (substr_count($body, '[图片]') > 4 ) {
            throw new HifoneException('最多只能选择4张图片');
        }
        $questionData['body_original'] = $questionData['body'];
        $question = $questionBll->updateQuestion($question, $questionData);

        return [
            'msg' => '编辑成功',
            'question' => $question
        ];
    }

    public function show(Question $question, QuestionBll $questionBll)
    {
        $question = $questionBll->showQuestion($question);

        return $question;
    }

    public function answers(Question $question, QuestionBll $questionBll, AnswerBll $answerBll)
    {
        $answerBll->checkQuestion($question->id);

        return $questionBll->sortAnswers($question);
    }

    //获取悬赏梯度
    public function rewards()
    {
        $rewards = explode(',', env('REWARDS') ? : '5,10,15,20');

        return ['rewards' => $rewards];
    }

    public function pin(Question $question, QuestionBll $questionBll)
    {
        return $questionBll->pin($question);
    }

    public function sink(Question $question, QuestionBll $questionBll)
    {
        return $questionBll->sink($question);
    }

    public function setExcellent(Question $question, QuestionBll $questionBll)
    {
        return $questionBll->setExcellent($question);
    }

}