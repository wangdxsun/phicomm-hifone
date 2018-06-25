<?php
namespace Hifone\Handlers\Listeners\Notification;

//问题审核通过时发送通知：通知关注我的用户，通知我@的用户
use Hifone\Events\Question\QuestionWasAuditedEvent;
use Hifone\Models\Question;

class SendQuestionNotificationHandler
{

    public function handle(QuestionWasAuditedEvent $event)
    {
        $this->newQuestionNotify($event->question);
    }

    public function newQuestionNotify(Question $question)
    {
        //通知关注我的用户
        $question->user->followedUsers()->chunk(100, function ($users) use ($question) {
            app('notifier')->batchNotify(
                'followed_user_new_question',
                $question->user,
                $users,
                $question
            );
        });

        $parserAt = app('parser.at');
        $parserAt->parse($question->body_original);
        app('notifier')->batchNotify(
            'question_mention',
            $question->user,
            $parserAt->users,
            $question
        );

    }
}