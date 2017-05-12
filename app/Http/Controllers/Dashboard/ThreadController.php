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
use Hifone\Models\Section;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Services\Parsers\Markdown;
use Redirect;
use View;
use Input;

class ThreadController extends Controller
{
    /**
     * Creates a new thread controller instance.
     *
     * @return void
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
        $threads = Thread::visible()->search($search)->with('node', 'user', 'lastOpUser')->orderBy('last_op_time', 'desc')->paginate(20);
        $threadAll = Thread::visible()->get();
        $userIds = array_unique(array_column($threadAll->toArray(), 'user_id'));
        $sections = Section::orderBy('order')->get();

        return View::make('dashboard.threads.index')
            ->withThreads($threads)
            ->withThreadAll($threadAll)
            ->withCurrentMenu('index')
            ->withSections($sections)
            ->withUsers(User::find($userIds));
    }

    /**
     * Shows the edit thread view.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(Thread $thread)
    {
        $sections = Section::orderBy('order')->get();

        return View::make('dashboard.threads.create_edit')
            ->withNode($thread->node)
            ->withSections($sections)
            ->withThread($thread)
            ->withCurrentMenu('index');
    }

    /**
     * Edit an thread.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Thread $thread)
    {
        $threadData = Input::get('thread');

        $threadData['body_original'] = $threadData['body'];
        $threadData['body'] = (new Markdown())->convertMarkdownToHtml($threadData['body']);
        $threadData['excerpt'] = Thread::makeExcerpt($threadData['body']);

        try {
            dispatch(new UpdateThreadCommand($thread, $threadData));
            $this->updateOpLog($thread, '修改帖子');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.thread.edit', $thread->id)
                ->withInput($threadData)
                ->withErrors($e->getMessageBag());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function pin(Thread $thread)
    {
        if($thread->order > 0){
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '取消置顶');
        } elseif($thread->order == 0){
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '置顶');
            event(new PinWasAddedEvent($thread->user, 'Thread'));
        } elseif($thread->order < 0){
            $thread->increment('order', 2);
            $this->updateOpLog($thread, '置顶');
            event(new PinWasAddedEvent($thread->user, 'Thread'));
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function sink(Thread $thread)
    {
        if($thread->order > 0){
            $thread->decrement('order', 2);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        } elseif($thread->order == 0){
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        } elseif($thread->order < 0){
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '取消下沉');
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function excellent(Thread $thread)
    {
        if($thread->is_excellent >0){
            $thread->decrement('is_excellent', 1);
            $this->updateOpLog($thread, '取消精华');
        }else{
            $thread->increment('is_excellent', 1);
            $this->updateOpLog($thread, '精华');
            event(new ExcellentWasAddedEvent($thread->user));
            event(new ThreadWasMarkedExcellentEvent($thread));
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function destroy(Thread $thread)
    {
        dispatch(new RemoveThreadCommand($thread));

        return Redirect::route('dashboard.thread.trash')
            ->withSuccess('恭喜，操作成功！');
    }

    /**
     * 待审核列表
     * @return mixed
     */
    public function audit()
    {
        $threads = Thread::audit()->with('node', 'user', 'lastOpUser')->orderBy('created_at', 'desc')->paginate(20);

        return view('dashboard.threads.audit')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withCurrentMenu('audit');
    }

    //从待审核列表审核通过帖子
    public function postAudit(Thread $thread)
    {
        if ($thread->node) {
            $thread->node->increment('thread_count', 1);
        }
        $thread->user->increment('thread_count', 1);
        event(new ThreadWasAddedEvent($thread));
        return $this->passAudit($thread);
    }

    //从回收站恢复帖子
    public function recycle(Thread $thread)
    {
        return $this->passAudit($thread);
    }

    //将帖子状态修改为审核通过
    public function passAudit($thread)
    {
        try {
            $thread->status = 0;
            $this->updateOpLog($thread, '审核通过');
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->getMessageBag());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function trash()
    {
        $search = $this->filterEmptyValue(Input::get('thread'));
        $threads = Thread::trash()->search($search)->with('node', 'user', 'lastOpUser')->orderBy('last_op_time', 'desc')->paginate(20);
        $threadAll = Thread::trash()->get();
        $userIds = array_unique(array_column($threadAll->toArray(), 'user_id'));
        $operators = array_unique(array_column($threadAll->toArray(), 'last_op_user_id'));
        $sections = Section::orderBy('order')->get();

        return view('dashboard.threads.trash')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withThreadAll($threadAll)
            ->withSections($sections)
            ->withCurrentMenu('trash')
            ->withUsers(User::find($userIds))
            ->withOperators(User::find($operators));
    }

    public function postTrash(Thread $thread)
    {
        try {
            $thread->status = -1;
            $this->updateOpLog($thread, trim(request('reason')));
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->getMessageBag());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }
}
