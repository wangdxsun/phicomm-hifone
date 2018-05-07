<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Question\QuestionWasAuditedEvent;
use Input;
use View;
use DB;
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
            ->with('current_menu', 'question')
            ->with('current_nav', 'index');

    }

    //待审核列表
    public function audit()
    {
        $questions = Question::audit()->orderBy('created_at', 'desc')->paginate(20);
        $questionsCount = Question::visible()->count();
        return View::make('dashboard.questions.audit')
            ->with('questionsCount', $questionsCount)
            ->with('questions', $questions)
            ->with('current_menu', 'question')
            ->with('current_nav', 'audit');
    }

    //回收站列表
    public function trash()
    {
        $search = $this->filterEmptyValue(Input::get('question'));
        $questions = Question::trash()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $questionsCount = Question::trash()->count();
        return View::make('dashboard.questions.trash')
            ->with('current_menu', 'question')
            ->with('current_nav', 'trash')
            ->with('questionsCount', $questionsCount)
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
    public function excellent(Question $question)
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

    //编辑问题(区分审核通过和审核中)
    public function edit(Question $question)
    {
        $menu = $question->status == Question::VISIBLE ? 'index' : 'audit';
        return View::make('dashboard.questions.create_edit')
            ->with('question', $question)
            ->with('current_menu', 'question')
            ->with('current_nav', $menu);

    }

    //后台编辑问题
    public function update(Question $question)
    {
        //修改问题标题，标签和内容
        $questionData = Input::get('question');
        $questionData['body_original'] = $questionData['body'];

        try {
            $this->updateOpLog($question, '后台编辑问题');
            //TODO 编辑问题后续的标签相关计数等

        } catch (\Exception $e) {
            return Redirect::route('dashboard.questions.edit', $question->id)
                ->withInput($questionData)
                ->withErrors($e->getMessage());
        }

        if ($question->status == Question::VISIBLE) {
            return Redirect::route('dashboard.questions.index')->withSuccess('恭喜，操作成功！');
        }
        return Redirect::route('dashboard.questions.audit')->withSuccess('恭喜，操作成功！');

    }



    //批量审核通过问题
    public function postBatchAudit() {
        $count = 0;
        $question_ids = Input::get('batch');
        if ($question_ids != null) {
            DB::beginTransaction();
            try {
                foreach ($question_ids as $id) {
                    self::postAudit(Question::find($id));
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
    public function postAudit(Question $question)
    {
        return $this->passAudit($question);
    }

    //将问题状态修改为审核通过,需要将问题数加1
    public function passAudit(Question $question)
    {
        DB::beginTransaction();
        try {
            $question->status = Question::VISIBLE;
            $question->save();
            $this->updateOpLog($question, '审核通过问题');
            $question->user->update(['question_count' => $question->user->questions()->visibleAndDeleted()->count()]);
            $questionForIndex = clone $question;
            $questionForIndex->addToIndex();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        //问题审核通过，加经验值
        event(new QuestionWasAuditedEvent($question->user, $question));

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


}