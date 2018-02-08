<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/2/5
 * Time: 16:00
 */

namespace Hifone\Handlers\Jobs;

use Hifone\Jobs\SayHello;

class SayHelloHandler
{
    public function handle(SayHello $sayHello)
    {
        \Log::debug('Hello '.$sayHello->name);
    }
}