<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Commands\Question\UpdateQuestionCommand;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Question\QuestionWasAuditedEvent;
use Hifone\Events\Question\QuestionWasDeletedEvent;
use Hifone\Models\TagType;
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
        $questions = Question::visible()->with(['user', 'tags'])->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $questionsCount = Question::visible()->count();
        //问题分类及相应问题子类
        $questionTagTypes = TagType::ofType([TagType::QUESTION])->with('tags')->get();
        return View::make('dashboard.questions.index')
            ->with('questions', $questions)
            ->with('questionsCount', $questionsCount)
            ->with('search', $search)
            ->with('questionTagTypes', $questionTagTypes)
            ->with('current_menu', 'question')
            ->with('current_nav', 'index');
    }

    //待审核列表
    public function audit()
    {
        //待审核列表，按发表时间倒序排序
        $questions = Question::audit()->with(['user', 'tags'])->orderBy('created_at', 'desc')->paginate(20);
        $questionsCount = Question::audit()->count();
        return View::make('dashboard.questions.audit')
            ->with('questionsCount', $questionsCount)
            ->with('questions', $questions)
            ->with('current_menu', 'question')
            ->with('current_nav', 'audit');
    }

    //回收站列表
    public function trashView()
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
            $question->update(['order' => -1]);
            $this->updateOpLog($question, '下沉问题');
            event(new SinkWasAddedEvent($question->user, $question));
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //加精问题
    public function setExcellent(Question $question)
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
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //编辑问题(区分审核通过和审核中)
    public function edit(Question $question)
    {
        $menu = $question->status == Question::VISIBLE ? 'index' : 'audit';
        $questionTags = $question->tags;
        //问题分类及相应问题子类
        $questionTagTypes = TagType::ofType([TagType::QUESTION])->with('tags')->get();
        return View::make('dashboard.questions.create_edit')
            ->with('question', $question)
            ->with('current_menu', 'question')
            ->with('questionTags', json_encode($questionTags->pluck('id')->toArray()))
            ->with('questionTagTypes', $questionTagTypes)
            ->with('current_nav', $menu);

    }

    //后台编辑问题
    public function update(Question $question)
    {
        //修改问题标题，标签和内容
        $this->validate(request(),[
            'question.title'  =>     'min:5|max:40',
            'question.body'   =>     'max:800',
        ],[
            'question.title.min' => '标题5'. '-'.'40个字符',
            'question.title.max' => '标题5'. '-'.'40个字符',
            'question.body.max'  => '内容0'. '-'.'800个字符',
        ]);
        $questionData = Input::get('question');
        $questionData['body_original'] = $questionData['body'];
        try {
            $question = dispatch( new UpdateQuestionCommand($question, $questionData));

        } catch (\Exception $e) {
            return Redirect::route('dashboard.question.edit', $question->id)
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
            $this->updateOpLog($question, '审核通过问题');
            $question->user->update(['question_count' => $question->user->questions()->visibleAndDeleted()->count()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        //问题审核通过，加经验值
        event(new QuestionWasAuditedEvent($question->user, $question));

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从审核通过删除帖子，需要将帖子数-1
    public function indexToTrash(Question $question)
    {
        DB::beginTransaction();
        try {
            $this->delete($question);
            $question->user->update(['question_count' => $question->user->questions()->visibleAndDeleted()->count()]);
            //扣除经验值
            event(new QuestionWasDeletedEvent($question->user, $question));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从待审核删除提问
    public function auditToTrash(Question $question)
    {
        try {
            $this->trash($question);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //审核通过列表的提问，放到回收站
    public function delete(Question $question)
    {
        $question->status = Question::DELETED;
        $this->updateOpLog($question, '删除提问', trim(request('reason')));
    }

    //审核未通过列表的提问，放到回收站
    public function trash(Question $question)
    {
        $question->status = Question::TRASH;
        $this->updateOpLog($question, '提问审核未通过', trim(request('reason')));
    }


}