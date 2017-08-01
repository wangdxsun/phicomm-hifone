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
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Events\Thread\ThreadWasAuditedEvent;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadController extends Controller
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

    public function store(ThreadBll $threadBll,WordsFilter $wordsFilter)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('对不起，你已被管理员禁止发言');
        }

        $thread = $threadBll->createThread();
        $post = $thread->body.$thread->title;
        if (Str::contains($post,['<img']) || Str::contains($post,['<a'])) {
            //帖子中包含图片或者链接，都需要审核
            return success('发表成功，待审核');
        }else if (($wordsFilter->filterWord($post))) {
            //自动审核未通过，需要人工审核
            return success('发表成功，待审核');
        }else{
            //自动审核通过，触发相应的代码逻辑
            event(new ThreadWasAddedEvent($thread));
            DB::beginTransaction();
            try {
                $thread->status = 0;
                $this->updateOpLog($thread, '审核通过');
                $thread->node->update(['thread_count' => $thread->node->threads()->visible()->count()]);
                $thread->user->update(['thread_count' => $thread->user->threads()->visible()->count()]);
                event(new ThreadWasAuditedEvent($thread));
                DB::commit();
            } catch (ValidationException $e) {
                DB::rollBack();
                throw new \Exception($e);
            }
            return [
                'msg' => '审核通过，发表成功',
                'thread' => $thread
            ];
        }
    }

    public function replies(Thread $thread, ThreadBll $threadBll)
    {
        return $threadBll->replies($thread);
    }
}
