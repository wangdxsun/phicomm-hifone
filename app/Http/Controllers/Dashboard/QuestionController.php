<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Models\Question;
use Input;
use View;
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
            $this->updateOpLog($question, '取消置顶');
        } else {
            $question->update(['order' => 1]);
            $this->updateOpLog($question, '置顶');
        }

    }

    //下沉问题
    public function sink(Question $question)
    {

    }

    //加精问题
    public function excellent(Question $question)
    {

    }

    //编辑问题
    public function edit(Question $question)
    {

    }

    //更新问题
    public function update(Question $question)
    {

    }

}