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
        $commonBll->login();
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();
        return $threads;
    }

    public function search(ThreadBll $threadBll)
    {
        $threads = $threadBll->search();

        return $threads;
    }

    public function store(ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('对不起，你已被管理员禁止发言');
        }
        $thread = $threadBll->createThreadInApp();

        $post = $thread->title.$thread->body;
        $badWord = '';
        if (Config::get('setting.auto_audit', 0) == 0 || ($badWord = $wordsFilter->filterWord($post)) || $threadBll->isContainsImageOrUrl($post)) {
            $thread->bad_word = $badWord;
            $msg = '帖子已提交，待审核';
        } else {
            $threadBll->autoAudit($thread);
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

    public function show(Thread $thread, ThreadBll $threadBll, CommonBll $commonBll)
    {
        $commonBll->login();
        $thread = $threadBll->showThread($thread);

        return $thread;
    }

    public function replies(Thread $thread)
    {
        return (new ThreadBll())->replies($thread);
    }
}