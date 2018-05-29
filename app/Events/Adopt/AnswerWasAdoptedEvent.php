<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Adopt;


final class AnswerWasAdoptedEvent implements AdoptEventInterface
{
    public $user;

    public $answer;

    public function __construct($user, $answer)
    {
        $this->user = $user;
        $this->answer = $answer;
    }
}
