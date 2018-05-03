<?php
namespace Hifone\Http\Controllers\Dashboard;

use Input;
use View;
use Hifone\Models\Answer;
use Hifone\Http\Controllers\Controller;

class AnswerController extends Controller
{
    //审核通过列表
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('question'));
        $answers = Answer::visible()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $answersCount = Answer::visible()->count();
        return View::make('dashboard.answers.index')
            ->with('answers', $answers)
            ->with('answersCount', $answersCount)
            ->with('search', $search)
            ->with('current_nav', 'index')
            ->with('current_menu', 'index');

    }


    public function audit()
    {

    }


    public function trash()
    {

    }
}