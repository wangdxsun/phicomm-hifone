<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Bll;

use Hifone\Models\Answer;

class AnswerBll extends BaseBll
{
    public function search($keyword)
    {
        $answers = Answer::searchAnswer($keyword)->load(['user', 'question'])->paginate(15);

        return $answers;
    }
}