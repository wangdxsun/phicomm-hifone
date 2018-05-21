<?php
/**
 * 更新关注问题的新回答数
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/18
 * Time: 16:59
 */

namespace Hifone\Handlers\Listeners\Question;

use Hifone\Events\Question\QuestionWasViewedEvent;

class UpdateFollowNewAnswerCountHandler
{
    public function handle(QuestionWasViewedEvent $event)
    {
        //TODO 关注该问题的用户，关注动态中该帖子的新回答数清零
    }

}