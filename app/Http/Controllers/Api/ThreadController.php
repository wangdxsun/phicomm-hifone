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

    public function show(Thread $thread, ThreadBll $threadBll)
    {
        $threadBll->showThread($thread);

        return $thread;
    }

    public function store(ThreadBll $threadBll)
    {
        $threadBll->createThread();

        return success('帖子发表成功，请耐心等待审核通过');
    }
}
