<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:37
 */

namespace Hifone\Http\Bll;

use Hifone\Models\Question;

class QuestionBll extends BaseBll
{
    public function questions()
    {
        $questions = Question::with(['user', 'tags'])->orderBy('order', 'desc')->recent()->paginate();

        return $questions;
    }
}