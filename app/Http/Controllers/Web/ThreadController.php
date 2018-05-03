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
use Hifone\Models\Option;
use Hifone\Models\Role;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Support\Facades\Redis;

class ThreadController extends WebController
{
    //帖子列表（首页最热）
    public function index(CommonBll $commonBll)
    {
        $commonBll->loginWeb();
        //置顶优先，再按热度值倒序排序
        $threads = Thread::visible()->with(['user', 'node'])->hot()->paginate();
        return $threads;
    }

    //帖子列表（首页最新）
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
        $thread = $threadBll->showThread($thread);

        return $thread;
    }

    //直接发帖 不同于 编辑草稿后发帖
    public function store(Thread $draft, ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        //防灌水
        $redisKey = 'thread_user:' . Auth::id();
        if (Redis::exists($redisKey)) {
            throw new HifoneException('发帖频繁，请稍后再试');
        }

        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $threadData = request('thread');
        if (1 == array_get($threadData, 'is_vote') && !(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))) {
            throw new HifoneException('普通用户暂不能发起投票');
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
        if (mb_strlen(strip_tags(array_get(request('thread'), 'body'))) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
        }
        //发布草稿为帖子
        if ($draft->exists) {
            if ($draft->status <> Thread::DRAFT) {
                throw new HttpException('当前不是草稿贴');
            }
            $thread = $threadBll->makeDraftToThread($draft, $threadData);
        } else {//直接发帖
            $thread = $threadBll->createThread($threadData);
        }

        $thread = $threadBll->auditThread($thread, $wordsFilter);
        if ($thread->is_vote == 1) {//投票贴
            $thread = $thread->load(['options']);
            foreach ($thread['options'] as $option) {
                $option['voted'] = Auth::check() ? Auth::user()->hasVoteOption($option) : false;
            }
            $thread['view_vote'] = $threadBll->canViewVote($thread);
            $thread['voted'] = $threadBll->isVoted($thread);
        }
        $msg = $thread->status == Thread::VISIBLE ? '发帖成功' : '帖子已提交，待审核';

        Redis::set($redisKey, $redisKey);
        Redis::expire($redisKey, 60);//设置发帖防灌水倒计时

        return [
            'msg' => $msg,
            'thread' => $thread
        ];
    }

    //存为草稿
    public function storeDraft(ThreadBll $threadBll)
    {
        $this->validate(request(), [
            'thread.body' => 'required|min:5',
        ], [
            'thread.body.required' => '帖子内容必填',
            'thread.body.min' => '帖子内容输入过少'
        ]);
        if (mb_strlen(strip_tags(array_get(request('thread'), 'body'))) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
        }
        $threadData = request('thread');
        $threadData['status'] = Thread::DRAFT;
        $threadBll->createDraft($threadData);

        return ['msg' => '保存成功'];
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
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        //修改帖子标题，版块和正文
        $threadData = request('thread');
        $threadData['node_id'] = SubNode::find($threadData['sub_node_id'])->node->id;

        if (!Auth::user()->hasRole(['Admin', 'Founder']) && Auth::id() <> $thread->user_id) {
            throw new HifoneException('您没有权限编辑这个帖子');
        }
        //web前端：管理员不能编辑用户审核中的帖子
        if (Auth::user()->hasRole(['Admin', 'Founder']) && Auth::id() <> $thread->user_id && $thread->status <> Thread::VISIBLE) {
            throw new HifoneException('您不能编辑用户审核中的帖子');
        }

        $this->updateOpLog($thread, '修改帖子');
        $thread = dispatch(new UpdateThreadCommand($thread, $threadData));

        //管理员编辑帖子免审核(含编辑自己待审核)，用户编辑后重新审核
        if (!Auth::user()->hasRole(['Admin', 'Founder'])) {
            $thread = $threadBll->auditThread($thread, $wordsFilter);
        }
        $msg = $thread->status == Thread::VISIBLE ? '发帖成功' : '帖子已提交，待审核';
        return [
            'msg' => $msg,
            'thread' => $thread
        ];
    }

    //编辑草稿
    public function updateDraft(Thread $thread, ThreadBll $threadBll)
    {
        if ($thread->status <> Thread::DRAFT) {
            throw new HifoneException('不能将帖子存为草稿');
        }
        $this->validate(request(), [
            'thread.body' => 'required|min:5',
        ], [
            'thread.body.required' => '帖子内容必填',
            'thread.body.min' => '帖子内容输入过少'
        ]);
        if (mb_strlen(strip_tags(array_get(request('thread'), 'body'))) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
        }
        if (Auth::id() == $thread->user_id) {
            $threadData = request('thread');
            $threadBll->updateDraft($thread, $threadData);
        } else {
            throw new HifoneException('您无权编辑该草稿');
        }

        return ['msg' => '保存成功'];
    }

    //删除草稿
    public function delete(Thread $thread)
    {
        if ($thread->status <> Thread::DRAFT) {
            throw new HifoneException('只能删除草稿');
        }
        if (Auth::id() == $thread->user_id) {
            $thread->delete();
            return ['msg' => '删除成功'];
        } else {
            throw new HifoneException('您无权删除该草稿');
        }
    }

    //投票贴设置用户权限
    public function voteLevels()
    {
        $levels = Role::userGroup()->orderBy('credit_low')->select('id', 'display_name')->get();

        return $levels;
    }

    //用户投票
    public function vote(Thread $thread, ThreadBll $threadBll)
    {
        if ($thread->is_vote <> 1) {
            throw new HifoneException('该帖子不具有投票功能');
        }
        $threadBll->vote($thread);

        return success('投票成功');
    }

    public function replies(Thread $thread, ThreadBll $threadBll, $sort = 'pinAndRecent')
    {
        //$sort : [pinAndRecent, like, desc, asc]
        return $threadBll->sortReplies($thread, $sort, 'web');
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
            event(new ExcellentWasAddedEvent($thread->user, $thread));
            event(new ThreadWasMarkedExcellentEvent($thread));
        }
        //更新热度值
        $thread->heat = $thread->heat_compute;
        $thread->save();
        return ['excellent' => $thread->is_excellent > 0 ? true : false];
    }

    public function pin(Thread $thread)
    {
        if ($thread->order == 1) {
            //已经是全局置顶，取消全局置顶
            $thread->update(['order' => 0]);
            $this->updateOpLog($thread, '取消置顶');
        } elseif ($thread->order == 0 ) {
            //不是全局置顶,判断是否是版块置顶
            if($thread->node_order == 1) {
                $thread->update(['node_order' => 0]);
            }
            $thread->update(['order' => 1]);
            $this->updateOpLog($thread, '置顶');
            event(new ThreadWasPinnedEvent($thread));
            event(new PinWasAddedEvent($thread->user,  $thread));
        } elseif ($thread->order < 0) {
            //全局下沉
            $thread->update(['order' => 1]);
            $this->updateOpLog($thread, '置顶');
            event(new ThreadWasPinnedEvent($thread));
            event(new PinWasAddedEvent($thread->user,  $thread));
        }
        return ['pin' => $thread->order > 0 ? true : false];
    }

    public function sink(Thread $thread)
    {
        if ($thread->order > 0) {
            $thread->decrement('order', 2);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user, $thread));
        } elseif ($thread->order == 0) {
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user, $thread));
        } elseif ($thread->order < 0) {
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '取消下沉');
        }
        return ['sink' => $thread->order < 0 ? true : false];
    }

    //管理员查看投票结果
    public function viewVoteResult(Thread $thread, Option $option, ThreadBll $threadBll)
    {
        $votes = $threadBll->viewVoteResult($thread, $option);

        return $votes;
    }

}
