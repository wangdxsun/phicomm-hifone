<?php
namespace Hifone\Commands\Credit;

final class DailyStatCommand
{
    public $action;


    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        //
    ];

    /**
     * Create a new add credit command instance.
     *
     * @param $action
     * @param $user
     */
    public function __construct($action)
    {
        $this->action = $action;
    }
}