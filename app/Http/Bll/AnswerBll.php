<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Commands\Answer\AddAnswerCommand;
use Hifone\Events\Adopt\AnswerWasAdoptedEvent;
use Hifone\Events\Answer\AnsweredWasAddedEvent;
use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Events\Invite\InviteWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Exceptions\Consts\AnswerEx;
use Hifone\Exceptions\Consts\QuestionEx;
use Hifone\Exceptions\HifoneException;
use Hifone\Jobs\RewardScore;
use Hifone\Models\Answer;
use Hifone\Models\Question;
use Hifone\Models\User;
use Auth;
use DB;
use Illuminate\Database\QueryException;

class AnswerBll extends BaseBll
{
    public function search($keyword)
    {
        $questions = Question::searchQuestionTitle($keyword)->load(['user', 'tags'])->paginate(15);
        foreach ($questions as $question) {
            $question->answer = Answer::searchAnswer($keyword, $question->id)->first();
        }

        return $questions;
    }

    public function showAnswer(Answer $answer)
    {
        if (!$answer->isVisible()) {
            throw new HifoneException('该回答已被删除', AnswerEx::DELETED);
        }
        $this->checkQuestion($answer->question_id);

        $answer = $answer->load(['user', 'question']);
        $answer->user['followed'] = User::hasFollowUser($answer->user);
        $answer['liked'] = Auth::check() ? Auth::user()->hasLikeAnswer($answer) : false;
        $answer['reported'] = Auth::check() ? Auth::user()->hasReportAnswer($answer) : false;

        return $answer;
    }

    public function sortComments(Answer $answer)
    {
        $this->checkQuestion($answer->question_id);
        $comments = $answer->comments()->visibleAndDeleted()
            ->with(['user', 'comment.user'])->orderBy('order', 'desc')->recent()->paginate();
        foreach ($comments as $comment) {
            $comment['liked'] = Auth::check() ? Auth::user()->hasLikeComment($comment) : false;
            $comment['reported'] = Auth::check() ? Auth::user()->hasReportComment($comment) : false;
        }

        return $comments;
    }

    public function createAnswer($answerData)
    {
        DB::beginTransaction();
        try {
            $answer = dispatch(new AddAnswerCommand(
                $answerData['body'],
                Auth::id(),
                $answerData['question_id'],
                get_request_agent(),
                getClientIp()
            ));

            if ($this->needNoAudit($answer)) {
                $this->autoAudit($answer);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        $answer = Answer::find($answer->id)->load(['user']);

        return $answer;
    }

    public function autoAudit(Answer $answer)
    {
        $answer->status = Answer::VISIBLE;
        $this->updateOpLog($answer, '自动审核通过');
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

        //回答审核通过，加经验值，更新关注人新通知数
        event(new AnswerWasAuditedEvent($answer->user, $answer));
        //提问被回答
        event(new AnsweredWasAddedEvent($answer->user, $answer->question));
    }

    public function needNoAudit(Answer $answer)
    {
        return !$this->hasVideo($answer->body) && !$this->hasUrl($answer->body) && !$this->hasImage($answer->body) && $answer->bad_word === '';
    }

    public function checkQuestion($questionId)
    {
        $question = Question::find($questionId);
        if (is_null($question)) {
            throw new HifoneException('问答不存在', QuestionEx::NOT_EXISTED);
        } elseif ($question->status <> Question::VISIBLE) {
            throw new HifoneException('该问答已被删除', QuestionEx::DELETED);
        }
    }

    public function checkInviteTimes(User $user)
    {
        if ($user->invites()->whereBetween('created_at', [Carbon::now()->subDay(1), Carbon::now()])->count() >= 15) {
            throw new HifoneException('24小时内最多邀请15人');
        }
    }

    public function createInvite(User $toUser, $question)
    {
        DB::beginTransaction();
        try {
            $toUser->inviters()->create([
                'from_user_id' => Auth::user()->id,
                'question_id' => $question->id,
            ]);
            DB::commit();
        } catch (QueryException $exception) {
            DB::rollBack();
            throw new HifoneException('已邀请', AnswerEx::INVITED);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        //邀请发通知
        event(new InviteWasAddedEvent(Auth::user(), $toUser, $question));
    }

    public function adoptAnswer(Answer $answer)
    {
        (new CommentBll)->checkAnswer($answer->id);
        //只可以采纳唯一回答，question answer_id，和answer adopted
        if ($answer->question->answer_id == null) {
            $answer->question->update(['answer_id' => $answer->id]);
        } else {
            throw new HifoneException('问题已采纳，请勿重复采纳', QuestionEx::HAS_ADOPTED);
        }
        $answer->update(['adopted' => 1]);

        //通知被采纳人
        event(new AnswerWasAdoptedEvent(Auth::user(), $answer->user, $answer));
        //给被采纳人加悬赏值
        dispatch(new RewardScore($answer->user, $answer->question->score));
    }

    public function pin(Answer $answer)
    {
        if ($answer->status <> Answer::VISIBLE) {
            throw new HifoneException('该回答已被删除', AnswerEx::DELETED);
        }
        //取消置顶
        if (1 == $answer->order) {
            $answer->update(['order' => 0]);
            $this->updateOpLog($answer, '取消置顶回答');
        } else {
            $answer->update(['order' => 1]);
            $this->updateOpLog($answer, '置顶回答');
            event(new PinWasAddedEvent($answer->user, $answer));
        }
        return ['pin' => $answer->order > 0 ? true : false];
    }

}