<?php
namespace Hifone\Handlers\Listeners\Score;

use Hifone\Events\Credit\LevelUpEvent;
use Hifone\Events\EventInterface;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Pin\NodePinWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Config;
use Auth;
use Hifone\Events\Thread\ThreadWasSharedEvent;
use Hifone\Events\Thread\ThreadWasUppedEvent;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Jobs\AddScore;
use Hifone\Models\Reply;
use Hifone\Models\Role;
use Hifone\Models\Thread;
use Illuminate\Foundation\Bus\DispatchesJobs;

//用来增加智慧果
class AddScoreHandler
{
    use DispatchesJobs;
    public function handle(EventInterface $event)
    {
        $action = '';
        $user = null;
        $object = '';
        $from = Auth::id();
        if ($event instanceof AvatarWasUploadedEvent) {
            //上传头像
            $action =  Config::get('setting.upload_avatar', null);
            $user = $event->target;
            $from = '';
            $object = date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        } elseif ($event instanceof RepliedWasAddedEvent) {
            //帖子被回复
            if (Auth::id() == $event->threadUser->id) {
                return false;//操作者和被操作者相同，不加智慧果
            }
            $action = Config::get('setting.thread_replied', null);
            $user = $event->threadUser;
            if (empty($event->threadUser) || $event->threadUser->id == $event->replyUser->id) {
                return false;
            }
            //回复的id
            $object = $event->reply->id;
        } elseif ($event instanceof PinWasAddedEvent) {
            //帖子和回复被置顶，增加智慧果
            if (Auth::id() == $event->object->user->id) {
                return false;//操作者和被操作者相同，不加智慧果
            } elseif($event->object instanceof Thread){
                $action = Config::get('setting.thread_pin', null);
                $object = $event->object->id;
            } elseif($event->object instanceof Reply){
                $action = Config::get('setting.reply_pin', null);
                //回复的id
                $object = $event->object->id;
            }
            $user = $event->user;
            $from = '';

        } elseif ($event instanceof LikedWasAddedEvent) {
            //帖子或回复被赞，增加智慧果
            $user = $event->user;
            if (Auth::id() == $user->id) {
                return false;//操作者和被操作者相同，不加智慧果
            } elseif ($event->class instanceof Thread) {
                $action = Config::get('setting.thread_liked', null);
                //帖子id
                $object = $event->class->id;
            } else {
                $action = Config::get('setting.reply_liked', null);
                //回复id
                $object = $event->class->id;

            }
        } elseif ($event instanceof ExcellentWasAddedEvent) {
            //帖子被加精
            if (Auth::id() == $event->target->id) {
                return false;//操作者和被操作者相同，不加智慧果
            } else {
                $action = Config::get('setting.thread_excellent', null);
                $user = $event->target;
                $object = $event->object->id;
                $from = '';
            }

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
                $object = $event->thread->id;
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
            $from = '';
            $object = date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        } elseif ($event instanceof ThreadWasUppedEvent) {
            if (Auth::id() == $event->thread->user_id) {
                //帖子被提升，需要加智慧果，自己提升自己的帖子不加
                return false;
            }
            $action = Config::get('setting.thread_upped', null);
            $user = $event->thread->user;
            $object = $event->thread->id;
            $from = '';
        } elseif ($event instanceof  ThreadWasSharedEvent) {
            $action = Config::get('setting.thread_shared', null);
            $user = Auth::user();
            $object = date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        } elseif ($event instanceof UserWasAddedEvent) {
            $user = $event->user;
            $action = Config::get('setting.user_added', null);
            $from = '';
            $object = date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        } elseif ($event instanceof NodePinWasAddedEvent) {
            $action = Config::get('setting.thread_pin', null);
            //帖子的id
            $object = $event->object->id;
            $user = $event->user;
            $from = '';
        }

        $this->apply($action, $user, $from, $object);
    }

    protected function apply($action, $user, $from, $object)
    {
        if (!$action || !$user || !$user->phicomm_id ) {
            return;
        }
        $job = (new AddScore($user, $action, $from, $object));
        $this->dispatch($job);
    }
}