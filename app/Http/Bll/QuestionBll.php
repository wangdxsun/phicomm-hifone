<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:37
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Question\AddQuestionCommand;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Jobs\RewardScore;
use Hifone\Models\Question;
use Auth;
use Hifone\Models\Tag;
use Hifone\Models\TagType;
use Hifone\Models\User;
use DB;
use Hifone\Services\Guzzle\Score;

class QuestionBll extends BaseBll
{
    public function questions($tagId)
    {
        if ($tagId) {
            $questions = Question::with(['user', 'tags'])->ofTag($tagId)->recent()->paginate();
        } else {
            $questions = Question::with(['user', 'tags'])->orderBy('order', 'desc')->recent()->paginate();
        }

        return $questions;
    }

    public function showQuestion(Question $question)
    {
        //todo 登录情况 清除关注该问题的新增回答数
        $question = $question->load(['user', 'tags']);
        $question->followed = Auth::check() ? Auth::user()->hasFollowQuestion($question) : false;
        $question->user->followed = Auth::check()? User::hasFollowUser($question->user) : false;
        $question->reported = Auth::check() ? Auth::user()->hasReportQuestion($question) : false;

        return $question;
    }

    public function sortAnswers(Question $question)
    {
        //置顶、采纳、时间倒序
        $answers = $question->answers()->visible()->with('user')
            ->orderBy('order', 'desc')->orderBy('adopted', 'desc')->recent()->paginate();

        return $answers;
    }

    public function createQuestion($questionData)
    {
        DB::beginTransaction();
        try {
            $question = dispatch(new AddQuestionCommand(
                $questionData['title'],
                $questionData['body'],
                $questionData['tagIds'],
                $questionData['score'],
                Auth::id(),
                get_request_agent(),
                getClientIp()
            ));
            //永久扣除用户智慧果
            dispatch(new RewardScore(Auth::user(), $questionData['score'] * -1));

            if ($this->needNoAudit($question)) {
                $this->autoAudit($question);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        $question = Question::find($question->id)->load(['user', 'tags']);

        return $question;
    }

    public function autoAudit(Question $question)
    {
        // todo 触发各类事件
        $question->status = Question::VISIBLE;
        $this->updateOpLog($question, '自动审核通过');
        $question->user->update(['question_count' => $question->user->questions()->visibleAndDeleted()->count()]);
    }

    public function needNoAudit(Question $question)
    {
        return !$this->hasVideo($question->body) && !$this->hasUrl($question->body) && !$this->hasImage($question->body) && $question->bad_word === '';
    }

    public function getValidTagIds($rawTagIds)
    {
        $tagIds = explode(',', $rawTagIds);
        $tagTypeIds = TagType::ofType(TagType::QUESTION)->pluck('id')->toArray();
        $tagIds = Tag::whereIn('id', $tagIds)->whereIn('tag_type_id', $tagTypeIds)->pluck('id')->toArray();

        return $tagIds;
    }

    public function search($keyword)
    {
        $questions = Question::searchQuestion($keyword)->load(['user', 'tags'])->paginate(15);

        return $questions;
    }
    
    //判断智慧果是否够用
    public function checkScore($phicommId)
    {
        $current = app(Score::class)->getScore($phicommId);

        $rewards = explode(',', env('REWARDS') ? : '5,10,15,20');
        $threshold = $rewards[0];

        if ($current < $threshold) {
            throw new HifoneException('智慧果不足');
        }
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
        return ['excellent' => $question->is_excellent > 0 ? true : false];
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
        return ['pin' => $question->order > 0 ? true : false];
    }
}