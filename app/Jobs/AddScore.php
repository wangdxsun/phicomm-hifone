<?php
namespace Hifone\Jobs;

use Hifone\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddScore extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    public $user;
    public $action;
    public $from;
    public $object;

    public function __construct(User $user, $action, $from, $object)
    {
        $this->user = $user;
        $this->action = $action;
        $this->from = $from;
        $this->object = $object;
    }

}