<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadController extends ApiController
{
    public function index(ThreadBll $threadBll)
    {
        $threads = $threadBll->getThreads();

        return $threads;
    }

    public function show(Thread $thread)
    {
        if ($thread->inVisible()) {
            throw new NotFoundHttpException('帖子状态不可见');
        }
        $thread = $thread->load(['user', 'node']);
        $replies = $thread->replies()->visible()->with(['user', 'likes'])
            ->orderBy('order', 'desc')->orderBy('created_at', 'desc')->paginate(15);
        $thread['followed'] = Auth::check() ? Auth::user()->isFollowUser($thread->user) : false;
        $thread['liked'] = Auth::check() ? Auth::user()->isLikedThread($thread) : false;
        $thread['replies'] = $replies;

        return $thread;
    }

    public function store(ThreadBll $threadBll)
    {
        $threadBll->createThread();

        return response()->json('帖子发表成功，请耐心等待审核通过');
    }
}
