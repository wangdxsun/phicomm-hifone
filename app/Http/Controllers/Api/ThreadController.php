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
use Config;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;

class ThreadController extends ApiController
{
    public function index(CommonBll $commonBll)
    {
        $commonBll->login();
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();

        return $threads;
    }

    public function search(ThreadBll $threadBll)
    {
        $threads = $threadBll->search();

        return $threads;
    }

    public function show(Thread $thread, ThreadBll $threadBll)
    {
        $threadBll->showThread($thread);

        return $thread;
    }

    public function store(ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('对不起，你已被管理员禁止发言');
        }

        $thread = $threadBll->createThread();
        $post = $thread->body.$thread->title;
        if (Config::get('setting.auto_audit', 0) == 0 || $threadBll->isContainsImageOrUrl($post) || $wordsFilter->filterWord($post)) {
            return [
                'msg' => '帖子已提交，待审核',
                'thread' => $thread
            ];
        }
        $threadBll->threadPassAutoAudit($thread);
        return [
            'msg' => '发布成功',
            'thread' => $thread
        ];
    }

    public function replies(Thread $thread, ThreadBll $threadBll)
    {
        return $threadBll->replies($thread);
    }
}
