<?php
namespace Hifone\Handlers\Listeners\Score;

use Hifone\Commands\Score\AddScoreCommand;
use Hifone\Events\Credit\LevelUpEvent;
use Hifone\Events\EventInterface;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Config;
use Auth;
use Hifone\Events\Thread\ThreadWasSharedEvent;
use Hifone\Events\Thread\ThreadWasUppedEvent;
use Hifone\Models\Role;
use Hifone\Models\Thread;
use Hifone\Models\User;

//用来增加智慧果
class AddScoreHandler
{
    public function handle(EventInterface $event)
    {
        $action = '';
        $user = null;
        if ($event instanceof AvatarWasUploadedEvent) {
            //上传头像
            $action =  Config::get('setting.upload_avatar', null);
            $user = $event->target;
        } elseif ($event instanceof RepliedWasAddedEvent) {
            //帖子被回复
            $action = Config::get('setting.thread_replied', null);
            $user = $event->threadUser;
            if (empty($event->threadUser) || $event->threadUser->id == $event->replyUser->id) {
                return false;
            }
        } elseif ($event instanceof PinWasAddedEvent) {
            //帖子和回复被置顶，增加智慧果
            if($event->action == 'Thread'){
                $action = Config::get('setting.thread_pin', null);
            } elseif($event->action == 'Reply'){
                $action = Config::get('setting.reply_pin', null);
            }
            $user = $event->user;
        } elseif ($event instanceof LikedWasAddedEvent) {
            //帖子或回复被赞，增加智慧果
            $user = $event->target;
            if (Auth::id() == $user->id) {
                return false;//操作者和被操作者相同，不加智慧果
            } elseif ($event->class instanceof Thread) {
                $action = Config::get('setting.thread_liked', null);
            } else {
                $action = Config::get('setting.reply_liked', null);
            }
        } elseif ($event instanceof ExcellentWasAddedEvent) {
            //帖子被加精
            $action = Config::get('setting.thread_excellent', null);
            $user = $event->target;
        } elseif ($event instanceof FollowedWasAddedEvent) {
            //用户被关注
            $user = $event->target;
            if (Auth::id() == $user->id) {
                return false;//操作者和被操作者相同，不加智慧果
            }
            $action = Config::get('setting.user_followed', null);
        } elseif ($event instanceof FavoriteWasAddedEvent) {
            $user = $event->thread->user;
            if (Auth::id() == $user->id) {
                //帖子被收藏，需要加智慧果，自己收藏自己的帖子不加
                return false;
            } else {
                $action = Config::get('setting.thread_favorited', null);
            }
        } elseif ($event instanceof LevelUpEvent) {
            //社区经验值等级提升，增加智慧果
            if ($event->user->score >= Role::where('name', 'Vip2')->first()->credit_low && $event->credit <= Role::where('name', 'Vip1')->first()->credit_high) {
                $action = Config::get('setting.level_up_2', null);
            } elseif ($event->user->score >= Role::where('name', 'Vip3')->first()->credit_low && $event->credit <= Role::where('name', 'Vip2')->first()->credit_high) {
                $action = Config::get('setting.level_up_3', null);
            } elseif ($event->user->score >= Role::where('name', 'Vip4')->first()->credit_low && $event->credit <= Role::where('name', 'Vip3')->first()->credit_high) {
                $action = Config::get('setting.level_up_4', null);
            } elseif ($event->user->score >= Role::where('name', 'Vip5')->first()->credit_low && $event->credit <= Role::where('name', 'Vip4')->first()->credit_high) {
                $action = Config::get('setting.level_up_5', null);
            } else {
                return false;
            }
            $user = $event->user;
        } elseif ($event instanceof ThreadWasUppedEvent) {
            if (Auth::id() == $event->thread->user_id) {
                //帖子被提升，需要加智慧果，自己提升自己的帖子不加
                return false;
            }
            $action = Config::get('setting.thread_upped', null);
            $user = $event->thread->user;
        } elseif ($event instanceof  ThreadWasSharedEvent) {
            if (Auth::id() == $event->thread->user_id) {
                //帖子被分享，需要加智慧果，自己分享自己的帖子不加
                return false;
            }
            $action = Config::get('setting.thread_shared', null);
            $user = $event->thread->user;
        }

        $this->apply($action, $user);
    }

    protected function apply($action, $user)
    {
        if (!$action || !$user || !$user->phicomm_id) {
            return;
        }
        $score = dispatch(new AddScoreCommand($action, $user));

        if (!$score) {
            return;
        }
    }
}