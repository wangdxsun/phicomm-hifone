<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/9
 * Time: 8:47
 */

namespace Hifone\Http\Bll;

use Auth;
use Config;
use Hifone\Events\User\UserWasActiveEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\User;

class UserBll extends BaseBll
{
    public function getCredits()
    {
        $credits = Auth::user()->credits()->where('body', '<>', '0')->with(['rule'])->recent()->paginate();

        return $credits;
    }

    public function getThreads(User $user)
    {
        //自己查看帖子，接口信息包括所有贴子;管理员和其他用户，只包括审核通过和审核通过被删除的贴子
        //个人中心按照帖子编辑时间排序
        if (Auth::check() && $user->id == Auth::id()) {
            $threads = $user->threads()->notDraft()->with(['user', 'node'])->recentEdit()->paginate();
        } else {
            $threads = $user->threads()->visibleAndDeleted()->with(['user', 'node'])->recentEdit()->paginate();
        }
        return $threads;
    }

    public function getReplies(User $user)
    {
        $replies = $user->replies()->visibleAndDeleted()->whereHas('thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['user', 'thread', 'reply.user'])->recent()->paginate();

        return $replies;
    }

    public function getDrafts(User $user)
    {
        if (Auth::check() && $user->id == Auth::id()) {
            $drafts = $user->threads()->draft()->with(['user', 'node'])->recentEdit()->paginate();
        } else {
            throw new HifoneException('您无权查看他人的草稿箱');
        }

        return $drafts;
    }

    //全局搜索用户
    public function search($keyword)
    {
        $users = User::searchUser($keyword)->paginate(15);
        foreach ($users as $user) {
            $user['followed'] = User::hasFollowUser($user);
        }
        return $users;
    }

    //个人收藏帖子列表
    public function getFavorites(User $user)
    {
        $threads = $user->favorites()->with(['thread.user', 'thread.node'])->has('thread')->paginate();
        return $threads;
    }

    //查看自己反馈历史记录（含所有状态）
    public function getThreadFeedbacks()
    {
        if (Auth::check()){
            $threadFeedbacks = Auth::user()->threads()->feedback()->recent()->paginate();
        } else {
            $threadFeedbacks = [];
        }
        return $threadFeedbacks;
    }

    //查看自己反馈历史记录（含所有状态）
    public function getReplyFeedbacks()
    {
        if (Auth::check()){
            $replyFeedbacks = Auth::user()->replies()->feedback()->recent()->paginate();
            foreach ($replyFeedbacks as $reply) {
                $reply['body_snapshot'] = strip_tags(app('parser.emotion')->reverseParseEmotionAndImage($reply->body));
            }
        } else {
            $replyFeedbacks = [];
        }
        return $replyFeedbacks;
    }

}