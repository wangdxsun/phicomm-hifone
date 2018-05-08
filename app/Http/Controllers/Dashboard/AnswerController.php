<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Events\Pin\PinWasAddedEvent;
use Input;
use DB;
use View;
use Redirect;
use Hifone\Models\Answer;
use Hifone\Http\Controllers\Controller;

class AnswerController extends Controller
{
    //审核通过列表
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('answer'));
        $answers = Answer::visible()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $answersCount = Answer::visible()->count();
        return View::make('dashboard.answers.index')
            ->with('answers', $answers)
            ->with('answersCount', $answersCount)
            ->with('search', $search)
            ->with('current_nav', 'index')
            ->with('current_menu', 'answer');
    }

    public function audit()
    {
        $answers = Answer::audit()->orderBy('last_op_time', 'desc')->paginate(20);
        $answersCount = Answer::audit()->count();
        return View::make('dashboard.answers.audit')
            ->with('answers', $answers)
            ->with('answersCount', $answersCount)
            ->with('current_nav', 'audit')
            ->with('current_menu', 'answer');
    }

    public function trash()
    {
        $search = $this->filterEmptyValue(Input::get('question'));
        $answers = Answer::trash()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $answersCount = Answer::trash()->count();
        return View::make('dashboard.answers.trash')
            ->with('search', $search)
            ->with('answers', $answers)
            ->with('answersCount', $answersCount)
            ->with('current_nav', 'trash')
            ->with('current_menu', 'answer');
    }

    public function edit(Answer $answer)
    {

    }

    public function update()
    {

    }

    public function pin(Answer $answer)
    {
        //1.取消置顶
        if (1 == $answer->order) {
            $answer->update(['order' => 0]);
            $this->updateOpLog($answer, '取消置顶回答');
        } else {
            $answer->update(['order' => 1]);
            $this->updateOpLog($answer, '置顶回答');
            event(new PinWasAddedEvent($answer->user, $answer));
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //批量审核通过问题
    public function postBatchAudit() {
        $count = 0;
        $answerIds = Input::get('batch');
        if ($answerIds != null) {
            DB::beginTransaction();
            try {
                foreach ($answerIds as $id) {
                    self::postAudit(Answer::find($id));
                    $count++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::back()->withErrors($e->getMessage());
            }
            return Redirect::back()->withSuccess('恭喜，批量操作成功！'.'共'.$count.'条');
        } else {
            return Redirect::back()->withErrors('您未选中任何记录！');
        }
    }

    //从待审核列表审核通过问题
    public function postAudit(Answer $answer)
    {
        return $this->passAudit($answer);
    }

    //将问题状态修改为审核通过,需要将问题数加1
    public function passAudit(Answer $answer)
    {
        DB::beginTransaction();
        try {
            $answer->status = Answer::VISIBLE;
            $answer->save();
            $this->updateOpLog($answer, '审核通过回答');
            $answer->user->update(['answer_count' => $answer->user->answers()->visibleAndDeleted()->count()]);
            $answerForIndex = clone $answer;
            $answerForIndex->addToIndex();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        //问题审核通过，加经验值
        //event(new QuestionWasAuditedEvent($answer->user, $answer));

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }
}