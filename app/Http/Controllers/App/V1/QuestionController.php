<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:21
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\QuestionBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Question;
use Auth;

class QuestionController extends AppController
{
    public function index(QuestionBll $questionBll)
    {
        $tagId = request('tag_id');
        $questions = $questionBll->questions($tagId);

        return $questions;
    }

    //悬赏问答（最新的5个提问）
    public function recent()
    {
        $questions = Question::with(['user', 'tags'])->recent()->limit(5)->get();

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
        //App图文混排
        $tagIds = $questionBll->getValidTagIds(request('tag_ids'));
        $bodies = json_decode(request('body'), true);
        $content = '';
        foreach ($bodies as $body) {
            if ($body['type'] == 'text') {
                $content.= "<p>".e($body['content'])."</p>";
            } elseif ($body['type'] == 'image') {
                $content.= "<img src='".$body['content']."'/>";
            }
        }
        if (mb_strlen(strip_tags($content)) > 800) {
            throw new HifoneException('请输入内容0~800个字');
        } elseif (count($tagIds) == 0) {
            throw new HifoneException('请选择标签哦~');
        } elseif (count($tagIds) > 4) {
            throw new HifoneException('最多选择4个标签哦~');
        }
        //todo 判断智慧果是否够用
        $questionData = [
            'title' => request('title'),
            'body' => $content,
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
        return ['rewards' => Question::$rewards];
    }
}