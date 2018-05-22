<?php
namespace Hifone\Jobs;

use Hifone\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//class Notify extends Job implements ShouldQueue
class Notify extends Job
{
    use InteractsWithQueue, SerializesModels;

    public $type;

    public $author;

    public $user;

    public $object;

    public $content;

    public function __construct($type, User $author, $user, $object, $content = null)
    {
        $this->type = $type;
        $this->author = $author;
        $this->user = $user;
        $this->object = $object;
        $this->content = $content;
    }

}