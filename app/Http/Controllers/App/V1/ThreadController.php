<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 16:45
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Events\Thread\ThreadWasViewedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Auth;
use Hifone\Services\Filter\WordsFilter;
use Config;
use Input;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadController extends AppController
{
    public function index(CommonBll $commonBll)
    {
        $commonBll->loginApp();
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();
        return $threads;
    }

    public function search($keyword, ThreadBll $threadBll)
    {
        $threads = $threadBll->search($keyword);
        return $threads;
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
            'thread.body' => 'required|min:5',
            'thread.sub_node_id' => 'required',
        ], [
            'thread.title.required' => '帖子标题必填',
            'thread.title.min' => '帖子标题不得少于5个字符',
            'thread.title.max' => '帖子标题不得多于40个字符',
            'thread.body.required' => '帖子内容必填',
            'thread.body.min' => '帖子内容不得少于5个字符',
        ]);
        if (count(strip_tags(array_get(request('thread'), 'body'))) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
        }
        $threadBll->appUpdateActiveTime();
        $thread = $threadBll->createThreadImageMixed();
        $result = $threadBll->auditThread($thread, $wordsFilter);
        return $result;
    }

    public function show(Thread $thread, ThreadBll $threadBll, CommonBll $commonBll)
    {
        $commonBll->loginApp();
        $threadBll->appUpdateActiveTime();
        $thread = $threadBll->showThread($thread);

        return $thread;
    }

    public function replies(Thread $thread)
    {
        return (new ThreadBll())->replies($thread);
    }

    public function feedback(ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $this->validate(request(), [
            'thread.title' => 'required|min:5|max:80',
            'thread.body' => 'required|min:5',
            'thread.sub_node_id' => 'required',
        ], [
            'thread.title.required' => '帖子标题必填',
            'thread.title.min' => '帖子标题不得少于5个字符',
            'thread.title.max' => '帖子标题不得多于80个字符',
            'thread.body.required' => '帖子内容必填',
            'thread.body.min' => '帖子内容不得少于5个字符',
        ]);
        if (count(strip_tags(array_get(request('thread'), 'body'))) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
        }
        $thread = $threadBll->createFeedback();
        $result = $threadBll->auditThread($thread, $wordsFilter);
        return $result;
    }
}