<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:21
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\QuestionBll;
use Hifone\Models\Question;

class QuestionController extends WebController
{
    public function index(QuestionBll $questionBll)
    {
        $tagId = request('tag_id');
        $questions = $questionBll->questions($tagId);

        return $questions;
    }

    public function getExcellent()
    {
        $questions = Question::with(['user', 'tags'])->orderBy('is_excellent', 'desc')->orderBy('order', 'desc')->recent()->limit(3)->get();

        return $questions;
    }

    public function store()
    {

    }

    public function show(Question $question)
    {
        return $question->load(['user', 'tags']);
    }
}