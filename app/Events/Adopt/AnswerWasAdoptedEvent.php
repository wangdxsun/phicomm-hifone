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
    public $from;

    public $to;

    public $answer;

    public function __construct($from, $to, $answer)
    {
        $this->from = $from;
        $this->to = $to;
        $this->answer = $answer;
    }
}
