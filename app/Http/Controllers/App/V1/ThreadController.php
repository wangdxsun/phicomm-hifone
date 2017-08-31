<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 16:45
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Events\Thread\ThreadWasViewedEvent;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Auth;

class ThreadController extends AppController
{
    public function index(CommonBll $commonBll)
    {
        $commonBll->login();
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();

        return $threads;
    }

    public function store()
    {

    }

    public function show(Thread $thread)
    {
        if ($thread->inVisible()) {
            throw new NotFoundHttpException('帖子状态不可见');
        }
        event(new ThreadWasViewedEvent($thread));

        $thread = $thread->load(['user']);
        $replies = $this->replies($thread);
        $thread['followed'] = User::hasFollowUser($thread->user);
        $thread['liked'] = Auth::check() ? Auth::user()->hasLikeThread($thread) : false;
        $thread['replies'] = $replies;

        return $thread;
    }

    public function replies($thread)
    {
        $replies = $thread->replies()->visible()->with(['user'])
            ->orderBy('order', 'desc')->orderBy('created_at', 'desc')->paginate();
        foreach ($replies as &$reply) {
            $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
        }

        return $replies;
    }
}