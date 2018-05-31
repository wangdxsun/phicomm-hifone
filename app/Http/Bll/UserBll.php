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
use GuzzleHttp\Client;
use Hifone\Events\User\UserWasActiveEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Question;
use Hifone\Models\Tag;
use Hifone\Models\User;
use Hifone\Services\Guzzle\Score;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function getQuestions(User $user)
    {
        $questions = $user->questions()->visibleAndDeleted()->with(['user','tags'])->recent()->paginate();

        return $questions;
    }

    public function getAnswers(User $user)
    {
        $answers = $user->answers()->visibleAndDeleted()->whereHas('question', function ($query) {
            $query->visibleAndDeleted();
        })->with(['user', 'question'])->recent()->paginate();

        return $answers;
    }

    //全局搜索用户
    public function search($keyword)
    {
        if (empty($keyword)) {
            $users = new LengthAwarePaginator([], 0, 15);
        } else {
            $users = User::searchUser($keyword)->paginate(15);
            foreach ($users as $user) {
                $user['followed'] = User::hasFollowUser($user);
            }
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
    public function getFeedbacks()
    {
        if (Auth::check()){
            $feedbacks = Auth::user()->threads()->feedback()->recent()->paginate();
        } else {
            $feedbacks = [];
        }
        return $feedbacks;
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

    public function getFollowNewAnswerCount(User $user)
    {
        return (int) $user->follows()->ofType(Question::class)->sum('answer_count');
    }

    //获取专家用户列表
    public function getExpertUsers(User $user, Question $question)
    {
        (new AnswerBll)->checkQuestion($question->id);
        $tag = Tag::findTagByName('专家');
        $search['tags'] = [array_get($tag, 'id')];
        $users = User::search($search)->select('id', 'avatar_url', 'username', 'answer_count', 'follower_count', 'role')->expert()->paginate(15);
        foreach ($users as $user) {
            $user['invited'] = $this->getInvitedStatus($user, $question);
        }

        return $users;
    }

    //获取关注用户列表(关注时间倒序)
    public function getFollowUsers(User $user, Question $question)
    {
        (new AnswerBll)->checkQuestion($question->id);
        $users = $user->followUsers()->paginate(15);
        foreach ($users as $user) {
            $user['invited'] = $this->getInvitedStatus($user, $question);
        }
        return $users;
    }

    //判断是否回答过该问题，是否被邀请回答该问题
    public function getInvitedStatus(User $toUser, Question $question)
    {
        if ($toUser->hasAnswerQuestion($question)) {
            return User::ANSWERED;
        }
        if ($toUser->hasBeenInvited($question)) {
            return User::INVITED;
        }
        return User::TO_INVITE;
    }

}