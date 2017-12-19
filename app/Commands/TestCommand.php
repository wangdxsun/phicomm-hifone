<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/11/9
 * Time: 19:17
 */

namespace Hifone\Commands;

use Collective\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestCommand implements ShouldQueue
{
    use Queueable;

    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function handle()
    {
        \Log::debug('handle', ['name' => $this->name]);
    }
}