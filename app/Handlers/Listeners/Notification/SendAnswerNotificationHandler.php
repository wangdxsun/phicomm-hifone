<?php
namespace Hifone\Handlers\Listeners\Notification;

use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Models\Answer;

//回答审核通过时通知：通知提问者，通知@的人，通知关注提问的人
class SendAnswerNotificationHandler
{
    public function handle(AnswerWasAuditedEvent $event)
    {
        $this->newAnswerNotify($event->answer);
    }

    public function newAnswerNotify(Answer $answer)
    {
        //通知提问者
        app('notifier')->notify(
            'question_new_answer',
            $answer->user,
            $answer->question->user,
            $answer,
            $answer->body
        );

        //通知@的人
        $parserAt = app('parser.at');
        $parserAt->parse($answer->body_original);

        // Notify mentioned users
        app('notifier')->batchNotify(
            'answer_mention',
            $answer->user,
            $parserAt->users,
            $answer,
            $answer->body
        );
    }
}