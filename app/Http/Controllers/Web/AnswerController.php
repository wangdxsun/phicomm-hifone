<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\AnswerBll;
use Illuminate\Pagination\LengthAwarePaginator;

class AnswerController extends WebController
{
    public function index()
    {

    }

    public function store()
    {

    }

    public function search($keyword, AnswerBll $answerBll)
    {
        if (empty($keyword)) {
            $answers = new LengthAwarePaginator([], 0, 15);
        } else {
            $answers = $answerBll->search($keyword);
        }

        return $answers;
    }
}