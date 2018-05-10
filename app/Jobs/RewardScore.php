<?php
namespace Hifone\Jobs;

use Hifone\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RewardScore extends Job
{
    use InteractsWithQueue, SerializesModels;
    public $user;
    public $score;

    public function __construct(User $user, $score)
    {
        $this->user = $user;
        $this->score = $score;
    }

}