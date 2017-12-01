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
        $thread = $threadBll->createThreadImageMixed();
        $result = $threadBll->auditThread($thread, $wordsFilter);
        return $result;
    }

    public function replies(Thread $thread, ThreadBll $threadBll)
    {
        return $threadBll->replies($thread, 'web');
    }
}
