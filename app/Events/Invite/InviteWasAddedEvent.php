<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Invite;

//$from邀请$to回答$question
final class InviteWasAddedEvent implements InviteEventInterface
{
    public $from;

    public $to;

    public $question;

    public function __construct($from, $to, $question)
    {
        $this->from = $from;
        $this->to = $to;
        $this->question = $question;
    }
}
