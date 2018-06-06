<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/4
 * Time: 10:33
 */

namespace Hifone\Handlers\Commands\Invite;

use Hifone\Commands\Invite\AddInviteCommand;
use Hifone\Events\Invite\InviteWasAddedEvent;


/**
 * @deprecated
 */
class AddInviteCommandHandler
{
    public function handle(AddInviteCommand $command)
    {
        $command->to->inviters()->create([
            'from_user_id' => $command->from->id,
            'question_id' => $command->question->id,
        ]);

        event(new InviteWasAddedEvent($command->from, $command->to, $command->question));
    }

}