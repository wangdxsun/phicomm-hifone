<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Commands\Answer\UpdateAnswerCommand;
use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Events\Answer\AnswerWasDeletedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\PinWasRemovedEvent;
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
        $answers = Answer::audit()->with(['user' ,'question.tags'])->orderBy('last_op_time', 'desc')->paginate(20);
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
        //修改问题标题，标签和内容
        $answerData = Input::get('answer');
        $answerData['body_original'] = $answerData['body'];
        try {
            //TODO 编辑问题后续的标签相关计数等
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
            event(new PinWasRemovedEvent($answer->user, $answer));
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
        //回答审核通过，加经验值
        if($answer->user->id != $answer->question->user->id) {
            event(new AnswerWasAuditedEvent($answer->user, $answer));
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


    //从审核通过删除回复，users表中需要将回复数-1
    public function indexToTrash(Answer $answer)
    {
        DB::beginTransaction();
        try {
            $this->delete($answer);
            $answer->user->update(['answer_count' => $answer->user->answers()->visibleAndDeleted()->count()]);
            event(new AnswerWasDeletedEvent($answer->user, $answer));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


    //从待审核删除提问
    public function auditToTrash(Answer $answer)
    {
        try {
            $this->trash($answer);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //审核通过列表的提问，放到回收站
    public function delete(Answer $answer)
    {
        $answer->status = Answer::DELETED;
        $this->updateOpLog($answer, '删除回复', trim(request('reason')));
    }

    //审核未通过列表的提问，放到回收站
    public function trash(Answer $answer)
    {
        $answer->status = Answer::TRASH;
        $this->updateOpLog($answer, '回复审核未通过', trim(request('reason')));
    }
}