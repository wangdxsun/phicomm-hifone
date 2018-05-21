<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Credit;

use Auth;
use Hifone\Commands\Credit\AddCreditCommand;
use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Events\Answer\AnswerWasDeletedEvent;
use Hifone\Events\EventInterface;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasRemovedEvent;
use Hifone\Events\Follow\FollowWasRemovedEvent;
use Hifone\Events\Image\ImageWasUploadedEvent;
use Hifone\Events\Like\LikeWasRemovedEvent;
use Hifone\Events\Pin\NodePinWasAddedEvent;
use Hifone\Events\Question\QuestionWasAuditedEvent;
use Hifone\Events\Question\QuestionWasDeletedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAuditedEvent;
use Hifone\Events\Reply\ReplyWasTrashedEvent;
use Hifone\Events\Report\ReportWasPassedEvent;
use Hifone\Events\Thread\ThreadWasAuditedEvent;
use Hifone\Events\Thread\ThreadWasTrashedEvent;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Events\User\UserWasLoggedinAppEvent;
use Hifone\Events\User\UserWasLoggedinEvent;
use Hifone\Events\Favorite\FavoritedWasAddedEvent;
use Hifone\Events\Favorite\FavoritedWasRemovedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Follow\FollowWasAddedEvent;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Follow\FollowedWasRemovedEvent;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Like\LikeWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Like\LikedWasRemovedEvent;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Hifone\Events\User\UserWasLoggedinWebEvent;
use Hifone\Models\Answer;
use Hifone\Models\Question;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class AddCreditHandler
{
    public function handle(EventInterface $event)
    {
        $action = '';
        $user = null;
        $object = null;
        if ($event instanceof ThreadWasAuditedEvent) {
            $action = 'thread_new';
            $user = $event->thread->user;
        } elseif ($event instanceof ThreadWasTrashedEvent) {
            $action = 'thread_removed';
            $user = $event->thread->user;
        } elseif ($event instanceof ReplyWasAuditedEvent) {
            $action = 'reply_new';
            $user = $event->reply->user;
        } elseif ($event instanceof ReplyWasTrashedEvent) {
            $action = 'reply_removed';
            $user = $event->reply->user;
        } elseif ($event instanceof RepliedWasAddedEvent) {
            $action = 'replied';
            $user = $event->threadUser;
            if (empty($event->replyUser) || $event->threadUser->id == $event->replyUser->id) {
                return false;
            }
        } elseif ($event instanceof ImageWasUploadedEvent) {
            $action = 'photo_upload';
            $user = Auth::user();
        } elseif ($event instanceof UserWasAddedEvent) {
            $action = 'register';
            $user = $event->user;
            //创建账号，积分
        } elseif ($event instanceof UserWasLoggedinEvent || $event instanceof UserWasLoggedinWebEvent || $event instanceof UserWasLoggedinAppEvent) {
            $action = 'login';
            $user = $event->user;
        } elseif ($event instanceof FavoritedWasAddedEvent) {
            $user = $event->object->user;
            if (Auth::id() == $user->id) {
                //帖子被收藏，需要加积分，自己的帖子被自己收藏不加分
                return false;
            } else {
                $action = 'favorited';
            }
        } elseif ($event instanceof FavoriteWasAddedEvent) {
            $user = $event->user;
            if (Auth::id() == $event->object->user->id) {
                //帖子被收藏，需要加积分，自己收藏自己的帖子不加分
                return false;
            }
            $action = 'favorite';
        } elseif ($event instanceof FavoritedWasRemovedEvent) {
            $user = $event->user;
            if (Auth::id() == $user->id) {
                //帖子被取消收藏，需要减积分，对自己操作无效
                return false;
            } else {
                $action = 'favorited_removed';
            }
        } elseif ($event instanceof FavoriteWasRemovedEvent) {
            $user = $event->user;
            if (Auth::id() == $event->object->user->id) {
                //取消收藏扣分，对自己的操作无效
                return false;
            }
            $action = 'favorite_removed';
        } elseif ($event instanceof PinWasAddedEvent) {
            if ($event->object instanceof Thread){
                $action = 'thread_pin';
            } elseif($event->object instanceof Reply){
                $action = 'reply_pin';
            } elseif ($event->object instanceof Question) {
                $action = 'question_pin';
            } elseif ($event->object instanceof Answer) {
                $action = 'answer_pin';
            }
            $user = $event->user;
            $object = $event->object;
        } elseif($event instanceof NodePinWasAddedEvent){
            $user = $event->user;
            $action = 'thread_node_pin';
        }  elseif ($event instanceof SinkWasAddedEvent) {
            if ($event->object instanceof Thread) {
                $action = 'thread_down';
            } elseif ($event->object instanceof Question) {
                $action = 'question_down';
            }
            $user = $event->user;
        } elseif ($event instanceof FollowWasAddedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'follow_thread';
            } else {
                $action = 'follow_user';
            }
            $user = Auth::user();
        } elseif ($event instanceof FollowWasRemovedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'follow_thread_removed';
            } else {
                $action = 'follow_user_removed';
            }
            $user = Auth::user();
        } elseif ($event instanceof FollowedWasAddedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'followed_thread';
                $user = $event->target->user;
            } else {
                $action = 'followed_user';
                $user = $event->target;
            }
        } elseif ($event instanceof FollowedWasRemovedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'followed_thread_removed';
                $user = $event->target->user;
            } else {
                $action = 'followed_user_removed';
                $user = $event->target;
            }
        } elseif ($event instanceof ExcellentWasAddedEvent) {
            if (Auth::id() == $event->object->user_id) {
                return false;//操作者和被操作者相同
            }
            if ($event->object instanceof Thread) {
                $action = 'thread_excellent';
            } elseif ($event->object instanceof Question) {
                $action = 'question_excellent';
            }
            $object = $event->object;
            $user = $event->user;
        } elseif ($event instanceof LikeWasAddedEvent) {
            if (Auth::id() == $event->object->user_id) {
                return false;//操作者和被操作者相同
            } else {
                $action = 'like';
            }
            $user = $event->user;
        } elseif ($event instanceof LikeWasRemovedEvent) {
            if (Auth::id() == $event->object->user_id) {
                return false;//操作者和被操作者相同
            } else {
                $action = 'like_removed';
            }
            $user = $event->user;
        } elseif ($event instanceof LikedWasAddedEvent) {
            $user = $event->user;
            if (Auth::id() == $user->id) {
                return false;//操作者和被操作者相同
            } else {
                $action = 'liked';
            }
        } elseif ($event instanceof LikedWasRemovedEvent) {
            $user = $event->user;
            if (Auth::id() == $user->id) {
                return false;//操作者和被操作者相同
            } else{
                $action = 'liked_removed';
            }
        } elseif ($event instanceof AvatarWasUploadedEvent) {
            $action = 'upload_avatar';
            $user = $event->target;
        } elseif ($event instanceof ReportWasPassedEvent) {//举报成功加积分
            $action = 'report';
            $user = $event->report->user;
        } elseif ($event instanceof QuestionWasAuditedEvent) {
            //问题审核通过
            $action = 'question_audited';
            $user = $event->user;
        } elseif ($event instanceof QuestionWasDeletedEvent) {
            //审核通过的提问被删除
            $action = 'question_deleted';
            $user = $event->user;
        } elseif ($event instanceof AnswerWasAuditedEvent) {
            //回答审核通过
            $action = 'answer_audited';
            $user = $event->user;
        } elseif ($event instanceof AnswerWasDeletedEvent) {
            //审核通过的回答被删除
            $action = 'answer_deleted';
            $user = $event->user;
        }

        $this->apply($object, $action, $user);
    }

    protected function apply($object, $action, $user)
    {
        if (!$action || !$user) {
            return;
        }
        $credit = dispatch(new AddCreditCommand($action, $user, $object));

        if (!$credit) {
            return;
        }
    }
}
