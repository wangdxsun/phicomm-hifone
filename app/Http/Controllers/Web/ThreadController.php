<?php

namespace Hifone\Http\Controllers\Web;

use Auth;
use Carbon\Carbon;
use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Thread\ThreadWasMarkedExcellentEvent;
use Hifone\Events\Thread\ThreadWasPinnedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;
use Hifone\Services\Parsers\Markdown;

class ThreadController extends WebController
{
    public function index(CommonBll $commonBll)
    {
        $commonBll->loginWeb();
        //置顶优先，再按热度值倒序排序
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();
        return $threads;
    }

    public function recent()
    {
        $threads = Thread::visible()->with(['user', 'node'])->recentEdit()->paginate();
        return $threads;
    }

    //首页精华帖子
    public function excellent()
    {
        $threads = Thread::visible()->with(['user', 'node'])->excellent()->paginate();
        return $threads;
    }

    public function search($keyword, ThreadBll $threadBll)
    {
        $threads = $threadBll->search($keyword);

        return $threads;
    }

    public function show(Thread $thread, ThreadBll $threadBll, CommonBll $commonBll)
    {
        $commonBll->loginWeb();
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
        ]);
        if (mb_strlen(strip_tags(array_get(request('thread'), 'body'))) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
        }
        $thread = $threadBll->createThread(request('thread'));
        $thread = $threadBll->auditThread($thread, $wordsFilter);
        $msg = $thread->status == Thread::VISIBLE ? '发布成功' : '帖子已提交，待审核';
        return [
            'msg' => $msg,
            'thread' => $thread
        ];
    }

    /**
     * 编辑帖子
     * 管理员：编辑任何用户的帖子，编辑后精华有效，不重新审核
     * 普通用户：只能编辑属于自己的帖子，编辑后精华失效，重新进入审核流程
     * @param Thread $thread
     * @return array
     * @throws HifoneException
     */
    public function update(Thread $thread, ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        //修改帖子标题，版块和正文
        $threadData = request('thread');
        $threadData['node_id'] = SubNode::find($threadData['sub_node_id'])->node->id;

        $threadData['body_original'] = $threadData['body'];
        $threadData['body'] = (new Markdown())->convertMarkdownToHtml($threadData['body']);
        $threadData['excerpt'] = Thread::makeExcerpt($threadData['body']);

        if (Auth::user()->hasRole(['Admin', 'Founder']) || Auth::id() == $thread->user_id) {
            $this->updateOpLog($thread, '修改帖子');
            $thread = dispatch(new UpdateThreadCommand($thread, $threadData));
            if (Auth::user()->hasRole(['Admin', 'Founder'])) {
                $msg = '恭喜，操作成功！';
            } else {
                $thread = $threadBll->auditThread($thread, $wordsFilter);
                $msg = $thread->status == Thread::VISIBLE ? '编辑成功' : '帖子已提交，待审核';
            }
            return [
                'msg' => $msg,
                'thread' => $thread
            ];
        } else {
            throw new HifoneException('您没有权限编辑这个帖子！');
        }
    }

    public function replies(Thread $thread, $sort, ThreadBll $threadBll)
    {
        //$sort : [like, desc, asc]
        return $threadBll->replies($thread, $sort, 'web');
    }

    public function setExcellent(Thread $thread)
    {
        if ($thread->is_excellent > 0) {
            $thread->decrement('is_excellent', 1);
            $this->updateOpLog($thread, '取消精华');
        } else {
            $thread->increment('is_excellent', 1);
            $thread->excellent_time = Carbon::now()->toDateTimeString();
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
        if ($thread->order > 0) {
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '取消置顶');
        } elseif ($thread->order == 0) {
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '置顶');
            event(new ThreadWasPinnedEvent($thread));
            event(new PinWasAddedEvent($thread->user, 'Thread'));
        } elseif ($thread->order < 0) {
            $thread->increment('order', 2);
            $this->updateOpLog($thread, '置顶');
            event(new ThreadWasPinnedEvent($thread));
            event(new PinWasAddedEvent($thread->user, 'Thread'));
        }

        return ['pin' => $thread->order > 0 ? true : false];
    }

    public function sink(Thread $thread)
    {
        if ($thread->order > 0) {
            $thread->decrement('order', 2);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        } elseif ($thread->order == 0) {
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        } elseif ($thread->order < 0) {
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '取消下沉');
        }
        return ['sink' => $thread->order < 0 ? true : false];
    }

}
