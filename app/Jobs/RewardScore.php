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
    public $name;

    //$score 增加积分传入正值，扣除积分传入负值
    public function __construct(User $user, $score, $name)
    {
        $this->user = $user;
        $this->score = $score;
        $this->name = $name;
    }

}