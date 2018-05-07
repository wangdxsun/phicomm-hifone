<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Input;
use View;
use Redirect;
use Hifone\Models\Question;
use Hifone\Http\Controllers\Controller;

class QuestionController extends Controller
{
    //审核通过列表
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('question'));
        $questions = Question::visible()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $questionsCount = Question::visible()->count();
        return View::make('dashboard.questions.index')
            ->with('questions', $questions)
            ->with('questionsCount', $questionsCount)
            ->with('search', $search)
            ->with('current_menu', 'index')
            ->with('current_nav', 'index');

    }

    //待审核列表
    public function audit()
    {
        $questions = Question::audit()->orderBy('created_at', 'desc')->paginate(20);
        return View::make('dashboard.questions.audit')
            ->with('questions', $questions);
    }

    //回收站列表
    public function trash()
    {
        $search = $this->filterEmptyValue(Input::get('question'));
        $questions = Question::trash()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        return View::make('dashboard.questions.trash')
            ->with('questions', $questions)
            ->with('search', $search);

    }

    //置顶问题
    public function pin(Question $question)
    {
        //1.取消置顶
        if (1 == $question->order) {
            $question->update(['order' => 0]);
            $this->updateOpLog($question, '取消置顶问题');
        } else {
            $question->update(['order' => 1]);
            $this->updateOpLog($question, '置顶问题');
            event(new PinWasAddedEvent($question->user, $question));
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');

    }

    //下沉问题
    public function sink(Question $question)
    {
        //1.取消下沉
        if ($question->order < 0) {
            $question->update(['order' => 0]);
            $this->updateOpLog($question, '取消下沉问题');
        } else {
            $question->update(['order' => 1]);
            $this->updateOpLog($question, '下沉问题');
            event(new SinkWasAddedEvent($question->user, $question));
        }
    }

    //加精问题
    public function setExcellent(Question $question)
    {
        //1.取消加精
        if ($question->is_excellent == 1) {
            $question->update(['is_excellent' => 0]);
            $this->updateOpLog($question, '取消问题加精');
        } else {
            $question->update(['is_excellent' => 1]);
            $this->updateOpLog($question, '加精问题');
            event(new ExcellentWasAddedEvent($question->user, $question));
        }
    }

    //编辑问题
    public function edit(Question $question)
    {
        $menu = $question->status == Question::VISIBLE ? 'index' : 'audit';
        return View::make('dashboard.questions.create_edit')
            ->withCurrentNav($menu);

    }

    //更新问题
    public function update(Question $question)
    {

    }

}