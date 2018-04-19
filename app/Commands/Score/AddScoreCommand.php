<?php


namespace Hifone\Commands\Score;

final class AddScoreCommand
{
    public $action;

    public $user;

    public $rules = [
        //
    ];
    public function __construct($action, $user)
    {
        $this->action = $action;
        $this->user = $user;
    }
}
