<?php
/**
 * 更新关注问题的新回答数
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/18
 * Time: 16:59
 */

namespace Hifone\Handlers\Listeners\Question;

use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Events\Answer\AnswerWasDeletedEvent;
use Hifone\Events\EventInterface;
use Hifone\Events\Question\QuestionWasViewedEvent;
use Auth;

class UpdateFollowNewAnswerCountHandler
{
    public function handle(EventInterface $event)
    {
        //TODO 关注该问题的用户，关注动态中该帖子的新回答数清零
        /**
         *  分三种情况讨论：
         *  1. QuestionWasViewedEvent 某条记录清零 当前查看用户关注该问题的计数；
         *  2. AnswerWasAuditedEvent 遍历加1 关注该回答所属问题的所有人的关注问题数
         *  3. AnswerWasDeletedEvent 遍历减1 关注该回答所属问题的所有人的关注问题数（砍掉）
         */
        if ($event instanceof QuestionWasViewedEvent) {
            $question = $event->question;
            if (Auth::check()) {
                $question->follows()->ofUser(Auth::id())->update(['answer_count' => 0]);
            }
        } elseif ($event instanceof AnswerWasAuditedEvent) {
            $event->answer->question->follows()->increment('answer_count', 1);
        }
    }

}