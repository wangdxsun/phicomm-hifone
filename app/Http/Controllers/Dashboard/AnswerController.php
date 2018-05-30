<?php
namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Commands\Answer\UpdateAnswerCommand;
use Hifone\Events\Answer\AnsweredWasAddedEvent;
use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Events\Answer\AnswerWasDeletedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Models\TagType;
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
        $answers = Answer::visible()->with(['user', 'question.tags'])->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $answersCount = Answer::visible()->count();
        //问题分类及相应问题子类
        $questionTagTypes = TagType::ofType([TagType::QUESTION])->with('tags')->get();
        return View::make('dashboard.answers.index')
            ->with('answers', $answers)
            ->with('answersCount', $answersCount)
            ->with('search', $search)
            ->with('questionTagTypes', $questionTagTypes)
            ->with('current_nav', 'index')
            ->with('current_menu', 'answer');
    }

    public function audit()
    {
        //待审核列表，按发表时间倒序排序
        $answers = Answer::audit()->with(['user' ,'question.tags'])->orderBy('created_at', 'desc')->paginate(20);
        $answersCount = Answer::audit()->count();
        return View::make('dashboard.answers.audit')
            ->with('answers', $answers)
            ->with('answersCount', $answersCount)
            ->with('current_nav', 'audit')
            ->with('current_menu', 'answer');
    }

    public function trashView()
    {
        $search = $this->filterEmptyValue(Input::get('answer'));
        $answers = Answer::trash()->with(['user', 'question.tags'])->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
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
        $menu = $answer->status == Answer::VISIBLE ? 'index' : 'audit';

        return View::make('dashboard.answers.create_edit')
            ->with('answer', $answer)
            ->with('current_menu', 'answer')
            ->with('current_nav', $menu);
    }

    public function update(Answer $answer)
    {
        //修改回答内容
        $this->validate(request(),[
            'answer.body'   =>     'min:5|max:800',
        ],[
            'answer.body.min' => '内容需5'. '-'.'800个字符',
            'answer.body.max' => '内容需5'. '-'.'800个字符',
        ]);
        $answerData = Input::get('answer');
        $answerData['body_original'] = $answerData['body'];
        try {
            $answer = dispatch( new UpdateAnswerCommand($answer, $answerData));
        } catch (\Exception $e) {
            return Redirect::route('dashboard.answer.edit', $answer->id)
                ->withInput($answerData)
                ->withErrors($e->getMessage());
        }

        if ($answer->status == Answer::VISIBLE) {
            return Redirect::route('dashboard.answers.index')->withSuccess('恭喜，操作成功！');
        }
        return Redirect::route('dashboard.answers.audit')->withSuccess('恭喜，操作成功！');
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

    //批量审核通过回答
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

    //从待审核列表审核通过回答
    public function postAudit(Answer $answer)
    {
        return $this->passAudit($answer);
    }

    //将回答状态修改为审核通过,需要将回答数加1
    public function passAudit(Answer $answer)
    {
        DB::beginTransaction();
        try {
            $answer->status = Answer::VISIBLE;
            $this->updateOpLog($answer, '审核通过回答');
            $answer->user->update(['answer_count' => $answer->user->answers()->visibleAndDeleted()->count()]);
            $answer->question->update([
                'answer_count' => $answer->question->answers()->visibleAndDeleted()->count(),
                'last_answer_time' => Carbon::now()->toDateTimeString()
            ]);
            //首次回答时间写入question->first_answer_time
            if ($answer->question->first_answer_time == null) {
                $answer->question->update([
                    'first_answer_time' => Carbon::now()->toDateTimeString()
                ]);
                //触发队列延时任务提醒作者三天后采纳 定时任务去做
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        //回答审核通过，加经验值, 更新关注人新通知数
        event(new AnswerWasAuditedEvent($answer->user, $answer));
        //提问被回答
        event(new AnsweredWasAddedEvent($answer->user, $answer->question));

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


    //从审核通过删除回复，users表中需要将回答数-1
    public function indexToTrash(Answer $answer)
    {
        DB::beginTransaction();
        try {
            $this->delete($answer);
            $answer->user->update(['answer_count' => $answer->user->answers()->visibleAndDeleted()->count()]);
            //回答删除，扣经验值, 更新关注人新通知数
            event(new AnswerWasDeletedEvent($answer->user, $answer));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


    //从待审核删除回答
    public function auditToTrash(Answer $answer)
    {
        try {
            $this->trash($answer);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //审核通过列表的回答，放到回收站
    public function delete(Answer $answer)
    {
        $answer->status = Answer::DELETED;
        $this->updateOpLog($answer, '删除回答', trim(request('reason')));
    }

    //审核未通过列表的回答，放到回收站
    public function trash(Answer $answer)
    {
        $answer->status = Answer::TRASH;
        $this->updateOpLog($answer, '回答审核未通过', trim(request('reason')));
    }
}