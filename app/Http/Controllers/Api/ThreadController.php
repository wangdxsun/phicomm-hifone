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
use Illuminate\Support\Facades\DB;

class ThreadController extends ApiController
{
    public function index(CommonBll $commonBll)
    {
        $commonBll->login();
        //置顶优先，再按热度值倒序排序
        $threads = Thread::visible()->with(['user', 'node'])->orderBy('order', 'DESC')->orderBy('heat', 'DESC')->paginate();
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
        $post = $thread->title.$thread->body;
        if (Config::get('setting.auto_audit', 0) == 0 || ($badWord = $wordsFilter->filterWord($post)) || $threadBll->isContainsImageOrUrl($post)) {
            if (isset($badWord)) {
                $thread->bad_word = $badWord;
            }
            $msg = '帖子已提交，待审核';
        } else {
            $threadBll->threadPassAutoAudit($thread);
            $msg = '发布成功';
        }
        $thread->body = app('parser.at')->parse($thread->body);
        $thread->body = app('parser.emotion')->parse($thread->body);
        $thread->save();
        return [
            'msg' => $msg,
            'thread' => $thread
        ];
    }

    public function replies(Thread $thread, ThreadBll $threadBll)
    {
        return $threadBll->replies($thread);
    }
}
