<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/2/5
 * Time: 11:07
 */

namespace Hifone\Handlers\Commands;

use Hifone\Commands\TestCommand;

class TestCommandHandler
{
    public function handle(TestCommand $command)
    {
        \Log::debug('TestCommandHandler', ['name' => $command->name]);
    }
}