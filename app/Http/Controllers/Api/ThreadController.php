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

use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Input;

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
        return $thread->load(['user', 'node', 'replies' => function ($query) {
            $query->where('status', Reply::VISIBLE);
        }, 'replies.user']);
    }

    public function store(ThreadBll $threadBll)
    {
        $threadBll->createThread();

        return response()->json('帖子发表成功，请耐心等待审核通过');
    }
}
