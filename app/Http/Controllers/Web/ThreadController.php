<?php

namespace Hifone\Http\Controllers\Web;

use Auth;
use Config;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Services\Filter\WordsFilter;
use DB;

class ThreadController extends WebController
{
    public function index(CommonBll $commonBll)
    {
        $commonBll->login();
        //置顶优先，再按热度值倒序排序
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();
        return $threads;
    }

    public function recent()
    {
        $threads = Thread::visible()->with(['user', 'node'])->recent()->paginate();
        return $threads;
    }

    public function search($keyword, ThreadBll $threadBll)
    {
        $threads = $threadBll->search($keyword);

        return $threads;
    }

    public function show(Thread $thread, ThreadBll $threadBll, CommonBll $commonBll)
    {
        $commonBll->login();
        $threadBll->showThread($thread);

        return $thread;
    }

    public function store(ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $this->validate(request(), [
            'thread.title' => 'required|min:5|max:40',
            'thread.body' => 'required|min:5|max:10000',
            'thread.sub_node_id' => 'required',
        ], [
            'thread.title.required' => '帖子标题必填',
            'thread.title.min' => '帖子标题不得少于5个字符',
            'thread.title.max' => '帖子标题不得多于40个字符',
            'thread.body.required' => '帖子内容必填',
            'thread.body.min' => '帖子内容不得少于5个字符',
            'thread.body.max' => '帖子内容不得多于10000个字符',
        ]);
        $thread = $threadBll->createThread();
        $result = $threadBll->auditThread($thread, $wordsFilter);
        return $result;
    }

    public function replies(Thread $thread, ThreadBll $threadBll)
    {
        return $threadBll->replies($thread, 'web');
    }

    public function excellent(Thread $thread)
    {
        if($thread->is_excellent > 0) {
            $thread->decrement('is_excellent', 1);
            $this->updateOpLog($thread, '取消精华');
        } else {
            $thread->increment('is_excellent', 1);
            $this->updateOpLog($thread, '精华');
            event(new ExcellentWasAddedEvent($thread->user));
            event(new ThreadWasMarkedExcellentEvent($thread));
        }
        //更新热度值
        $thread->heat = $thread->heat_compute;
        $thread->save();
        return ['excellent' => $thread->is_excellent > 0 ? true : false];
    }

    public function pin(Thread $thread)
    {
        if($thread->order > 0) {
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '取消置顶');
        } elseif($thread->order == 0) {
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '置顶');
            event(new ThreadWasPinnedEvent($thread));
            event(new PinWasAddedEvent($thread->user, 'Thread'));
        } elseif($thread->order < 0) {
            $thread->increment('order', 2);
            $this->updateOpLog($thread, '置顶');
            event(new ThreadWasPinnedEvent($thread));
            event(new PinWasAddedEvent($thread->user, 'Thread'));
        }

        return ['pin' => $thread->order > 0 ? true : false];
    }

    public function sink(Thread $thread)
    {
        if($thread->order > 0) {
            $thread->decrement('order', 2);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        } elseif($thread->order == 0) {
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        } elseif($thread->order < 0) {
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '取消下沉');
        }
        return ['sink' => $thread->order < 0 ? true : false];
    }

}
