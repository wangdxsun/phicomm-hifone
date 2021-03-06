<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:21
 */

namespace Hifone\Http\Controllers\App\V1;

use Carbon\Carbon;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\AnswerBll;
use Hifone\Http\Bll\QuestionBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Question;
use Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $questions = Question::select('id', 'title', 'thumbnails')->visible()->recent()->limit(5)->get();

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
            'score'=> 'required|int'
        ]);
        //App图文混排
        $bodies = json_decode(request('body'), true);
        $content = $this->makeMixedContent($bodies);

        $tagIds = $questionBll->getValidTagIds(request('tag_ids'));
        if (mb_strlen(strip_tags($content)) > 800) {
            throw new HifoneException('请输入内容0~800个字');
        } elseif (count($tagIds) == 0) {
            throw new HifoneException('请选择标签哦~');
        } elseif (count($tagIds) > 4) {
            throw new HifoneException('最多选择4个标签哦~');
        }
        //TODO 管理员发表提问，因为管理员没有phicommId无法获取智慧果
        $questionBll->checkScore(Auth::user()->phicomm_id);

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
        $question['in_adopt_period'] = $question->first_answer_time == null ? false : $question->first_answer_time < Carbon::now() && Carbon::now() < $question->first_answer_time->addDays(5);

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

}