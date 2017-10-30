<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Console\Command;

class GetExcellentUser extends Command
{
    protected $signature = 'get:excellentUsers';

    protected $description = 'get excellent users';

    public function __construct()
    {
        parent::__construct();
    }

    //根据被点赞数，被评论数和被收藏数筛选优质用户
    public function handle()
    {
        //首先拿到帖子找到发帖人,发帖时间段(周一0点到周日0点)
        $lastMonday = Carbon::today()->previousWeekendDay()->subDay(6)->toDateTimeString();
        $lastSunday = Carbon::today()->previousWeekendDay()->toDateTimeString();
        $threads = Thread::visible()->whereBetween('created_at',[$lastMonday,$lastSunday])
            ->get()->unique('user_id');
        dd($lastMonday,$lastSunday,$threads);
        foreach ($threads as $thread) {
            $user = User::find($thread->user_id);
            dd($user);
        }
    }
}