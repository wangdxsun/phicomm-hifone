<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\CreditRule;
use Hifone\Models\Tag;
use Hifone\Models\User;
use Illuminate\Console\Command;

class AddAutoTag extends Command
{
    protected $signature = 'add:autoTag';

    protected $description = 'add auto tags for users';

    public function __construct()
    {
        parent::__construct();
    }

    //根据用户信息，为用户自动打标签
    public function handle()
    {

        $tagData = [];
        $startDay = Carbon::today()->toDateTimeString();
        $previousTwoWeeks = Carbon::today()->subDay(14)->toDateTimeString();
        $previousFourWeeks = Carbon::today()->subDay(28)->toDateTimeString();
        $previousEightWeeks = Carbon::today()->subDay(56)->toDateTimeString();
        //为注册时间在两周内的用户添加'新用户'标签
        User::whereHas('credits', function($query) use ($startDay, $previousTwoWeeks) {
            $query->where('rule_id', CreditRule::where('slug', 'register')->first()->id)->whereBetween('created_at',[$previousTwoWeeks, $startDay]);
        })->chunk(100, function ($users) use ($tagData) {
            foreach ($users as $user) {
                array_push($tagData, Tag::where('name', '新用户')->first()->id);
                $user->tags()->sync($tagData);
            }
        });
        //为非新用户添加'活跃'和'内容'标签
        User::whereHas('credits', function($query) use ($startDay, $previousTwoWeeks) {
            $query->where('rule_id', CreditRule::where('slug', 'login')->first()->id)->whereBetween('created_at',[$previousTwoWeeks, $startDay])->groupBy('user_id');
        })->chunk(100, function ($users) use ($tagData) {
            foreach ($users as $user) {
                array_push($tagData, Tag::where('name', '近期登陆')->first()->id);
                $user->tags()->sync($tagData);
            }
        });




        //为所有的用户添加产品的标签

    }
}