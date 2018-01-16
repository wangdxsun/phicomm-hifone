<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use AltThree\Validator\ValidationException;
use Hifone\Commands\Thread\RemoveThreadCommand;
use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Events\Thread\ThreadWasMarkedExcellentEvent;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Services\Parsers\Markdown;
use DB;
use Redirect;
use View;
use Input;
use Hifone\Events\Thread\ThreadWasPinnedEvent;
use Hifone\Events\Thread\ThreadWasAuditedEvent;
use Hifone\Events\Thread\ThreadWasTrashedEvent;

class ThreadController extends Controller
{
    /**
     * Creates a new thread controller instance.
     *
     */
    public function __construct()
    {
        View::share([
            'sub_header'   => '帖子管理'
        ]);
    }

    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('thread'));
        if (array_key_exists('sub_node_id',$search) && array_key_exists('node_id',$search) && $search['node_id'] != SubNode::find($search['sub_node_id'])->node->id) {
            return Redirect::route('dashboard.thread.index')->withErrors('主版块信息和子版块信息不一致！');
        }
        $threads = Thread::visible()->search($search)->with('node', 'user', 'lastOpUser', 'subNode')->orderBy('last_op_time', 'desc')->paginate(20);
        $sections = Section::orderBy('order')->get();
        $nodes = Node::orderBy('order')->get();
        $orderTypes = Thread::$orderTypes;
        $threadCount = Thread::visible()->count();
        return View::make('dashboard.threads.index')
            ->withThreads($threads)
            ->with('threadCount', $threadCount)
            ->with('orderTypes', $orderTypes)
            ->withCurrentNav('index')
            ->withSearch($search)
            ->withNodes($nodes)
            ->withSections($sections);
    }

    /**
     * 待审核列表
     * @return mixed
     */
    public function audit()
    {
        $threads = Thread::audit()->with('node', 'user', 'lastOpUser', 'subNode')->orderBy('created_at', 'desc')->paginate(20);
        $threadCount = Thread::audit()->count();

        return view('dashboard.threads.audit')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->with('threadCount', $threadCount)
            ->withCurrentNav('audit');
    }

    /**
     * 回收站列表
     * @return mixed
     */
    public function trashView()
    {
        $search = $this->filterEmptyValue(Input::get('thread'));
        $threads = Thread::trash()->search($search)->with('node', 'user', 'lastOpUser')->orderBy('last_op_time', 'desc')->paginate(20);
        $threadCount = Thread::trash()->count();
        $threadAll = Thread::trash()->get();
        $userIds = array_unique(array_column($threadAll->toArray(), 'user_id'));
        $operators = array_unique(array_column($threadAll->toArray(), 'last_op_user_id'));
        $sections = Section::orderBy('order')->get();
        $orderTypes = Thread::$orderTypes;

        return view('dashboard.threads.trash')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->with('threadCount', $threadCount)
            ->withThreads($threads)
            ->withThreadAll($threadAll)
            ->with('orderTypes',$orderTypes)
            ->withSections($sections)
            ->withCurrentNav('trash')
            ->withSearch($search)
            ->withUsers(User::find($userIds))
            ->withOperators(User::find($operators));
    }

    public function edit(Thread $thread)
    {
        $nodes = Node::orderBy('order')->get();

        $menu = $thread->status == Thread::VISIBLE ? 'index' : 'audit';
        return View::make('dashboard.threads.create_edit')
            ->withNodes($nodes)
            ->withThread($thread)
            ->withCurrentNav($menu);
    }

    /**
     * Edit an thread.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Thread $thread)
    {
        $threadData = Input::get('thread');
        $threadData['node_id'] = SubNode::find($threadData['sub_node_id'])->node->id;

        $threadData['body_original'] = $threadData['body'];
        $threadData['body'] = (new Markdown())->convertMarkdownToHtml($threadData['body']);
        $threadData['excerpt'] = Thread::makeExcerpt($threadData['body']);

        try {
            $this->updateOpLog($thread, '修改帖子');
            dispatch(new UpdateThreadCommand($thread, $threadData));
        } catch (\Exception $e) {
            return Redirect::route('dashboard.thread.edit', $thread->id)
                ->withInput($threadData)
                ->withErrors($e->getMessage());
        }
        if ($thread->status == Thread::VISIBLE) {
            return Redirect::route('dashboard.thread.index')->withSuccess('恭喜，操作成功！');
        }
        return Redirect::route('dashboard.thread.audit')->withSuccess('恭喜，操作成功！');
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

        return Redirect::back()->withSuccess('恭喜，操作成功！');
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
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function excellent(Thread $thread)
    {
        if ($thread->is_excellent > 0) {
            $thread->is_excellent = 0;
            $this->updateOpLog($thread, '取消精华');
        } else {
            $thread->is_excellent = 1;
            $this->updateOpLog($thread, '精华');
            event(new ExcellentWasAddedEvent($thread->user));
            event(new ThreadWasMarkedExcellentEvent($thread));
        }
        //更新热度值
        $thread->heat = $thread->heat_compute;
        $thread->save();
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function destroy(Thread $thread)
    {
        dispatch(new RemoveThreadCommand($thread));

        return Redirect::route('dashboard.thread.trash')
            ->withSuccess('恭喜，操作成功！');
    }

    //批量审核通过帖子
    public function postBatchAudit() {
        $count = 0;
        $thread_ids = Input::get('batch');
        if ($thread_ids != null) {
            DB::beginTransaction();
            try {
                foreach ($thread_ids as $id) {
                    self::postAudit(Thread::find($id));
                    $count++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::back()->withErrors($e->getMessage());
            }
            return Redirect::back()->withSuccess('恭喜，批量操作成功！'.'共'.$count.'条');
        } else {
            return Redirect::back()->withErrors('您未选中任何记录！');
        }
    }

    //从待审核列表审核通过帖子
    public function postAudit(Thread $thread)
    {
        event(new ThreadWasAddedEvent($thread));
        return $this->passAudit($thread);
    }

    //从回收站恢复帖子
    public function recycle(Thread $thread)
    {
        return $this->passAudit($thread);
    }

    //将帖子状态修改为审核通过,需要将帖子数加1
    public function passAudit(Thread $thread)
    {
        DB::beginTransaction();
        try {
            $thread->status = Thread::VISIBLE;
            $thread->heat = $thread->heat_compute;
            $this->updateOpLog($thread, '审核通过');
            $thread->node->update(['thread_count' => $thread->node->threads()->visible()->count()]);
            if ($thread->subNode) {
                $thread->subNode->update(['thread_count' => $thread->subNode->threads()->visible()->count()]);
            }
            $thread->user->update(['thread_count' => $thread->user->threads()->visibleAndDeleted()->count()]);
            event(new ThreadWasAuditedEvent($thread));
            $thread->addToIndex();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从审核通过删除帖子，需要将帖子数-1
    public function indexToTrash(Thread $thread)
    {
        DB::beginTransaction();
        try {
            $this->delete($thread);
            $thread->node->update(['thread_count' => $thread->node->threads()->visible()->count()]);
            $thread->subNode->update(['thread_count' => $thread->subNode->threads()->visible()->count()]);
            $thread->user->update(['thread_count' => $thread->user->threads()->visibleAndDeleted()->count()]);
            $thread->removeFromIndex();
            event(new ThreadWasTrashedEvent($thread));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从待审核删除帖子
    public function auditToTrash(Thread $thread)
    {
        try {
            $this->trash($thread);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //删除正常帖子，放到回收站
    public function delete(Thread $thread)
    {
        $thread->status = Thread::DELETED;
        $this->updateOpLog($thread, '删除帖子', trim(request('reason')));
    }

    //回复审核未通过，放到回收站
    public function trash(Thread $thread)
    {
        $thread->status = Thread::TRASH;
        $this->updateOpLog($thread, '帖子审核未通过', trim(request('reason')));
    }

    public function getHeatOffset(Thread $thread)
    {
        if ($thread->heat_offset != null) {
            return $thread->heat_offset;
        }
        return 0;
    }

    public function setHeatOffset(Thread $thread)
    {
        $heatOffset = request('value');
        try {
            $thread->heat_offset = $heatOffset;
            $thread->heat = $thread->heat_compute;
            $this->updateOpLog($thread, '提升帖子', $heatOffset);
            $thread->save();
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //在审核通过页面，批量移动帖子到别的版块
    public function batchMoveThread()
    {
        $count = 0;
        $threadData = Input::get('thread');
        $threadData['node_id'] = SubNode::find($threadData['sub_node_id'])->node->id;
        $threadIds = Input::get('batch');
        if (!$threadIds) {
            return Redirect::back()->withErrors('您未选中任何记录！');
        }
        DB::beginTransaction();
        try {
            foreach ($threadIds as $threadId) {
                self::moveThread(Thread::find($threadId), $threadData);
                $count++;
            }
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return  Redirect::back()->withSuccess('恭喜，成功将'.$count.'个帖子移动到相应子版块！');
    }

    //移动帖子,将帖子移入别的版块
    public function moveThread(Thread $thread, $threadData)
    {
        DB::beginTransaction();
        try {
            dispatch(new UpdateThreadCommand($thread, $threadData));
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
    }
}
