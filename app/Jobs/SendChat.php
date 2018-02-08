<?php
namespace Hifone\Jobs;

use Hifone\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChat extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    public $from;
    public $to;
    public $message;

    public function __construct(User $from, User $to, $message)
    {
        $this->from = $from;
        $this->to = $to;
        $this->message = $message;
    }

}