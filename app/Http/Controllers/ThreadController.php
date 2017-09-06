<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use AltThree\Validator\ValidationException;
use Auth;
use Hifone\Commands\Append\AddAppendCommand;
use Hifone\Commands\Thread\RemoveThreadCommand;
use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Events\Thread\ThreadWasViewedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Http\Bll\ThreadBll;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Repositories\Criteria\Thread\BelongsToNode;
use Config;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Support\Facades\DB;
use Input;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Hifone\Events\Thread\ThreadWasPinnedEvent;

class ThreadController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index(ThreadBll $threadBll)
    {
        $threads = $threadBll->getThreads();
        return $this->view('threads.index')
            ->withThreads($threads)
            ->withSections(Section::orderBy('order')->get());
    }

    /**
     * Shows a thread in more detail.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\View\View
     */
    public function show(Thread $thread)
    {
        if ($thread->inVisible()) {
            throw new NotFoundHttpException;
        }

        $this->breadcrumb->push([
            $thread->node->name => $thread->node->url,
            $thread->title      => $thread->url,
        ]);

        $replies = $thread->replies()->visible()->with(['user'])
            ->orderBy('order', 'desc')->orderBy('id', 'asc')
            ->paginate(Config::get('setting.replies_per_page', 30));

        $repository = app('repository');
        $repository->pushCriteria(new BelongsToNode($thread->node_id));
        $nodeThreads = $repository->model(Thread::class)->getThreadList(5);

        event(new ThreadWasViewedEvent($thread));
        return $this->view('threads.show')
            ->withThread($thread)
            ->withReplies($replies)
            ->withNodeThreads($nodeThreads)
            ->withNode($thread->node);
    }

    /**
     * Shows the add thread view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $sections = Section::orderBy('order')->get();
        $subNodes = SubNode::find(Input::query('sub_node_id'));

        $this->breadcrumb->push(trans('hifone.threads.add'), route('thread.create'));

        return $this->view('threads.create_edit')
            ->withSections($sections)
            ->with('subNodes',$subNodes);
    }

    public function store(ThreadBll $threadBll, WordsFilter $wordsFilter)
    {
        if (Auth::user()->hasRole('NoComment')) {
            return Redirect::back()->withErrors('您已被系统管理员禁言');
        }
        try {
            $thread = $threadBll->createThread();
            $thread->heat = $thread->heat_compute;
            $post = $thread->body . $thread->title;
            if (Config::get('setting.auto_audit', 0) == 0 || ($badWord = $wordsFilter->filterWord($post)) || $threadBll->isContainsImageOrUrl($post)) {
                if (isset($badWord)) {
                    $thread->bad_word = $badWord;
                }
                $thread->body = app('parser.at')->parse($thread->body);
                $thread->body = app('parser.emotion')->parse($thread->body);
                $thread->save();
                return Redirect::route('thread.index')
                    ->withSuccess('帖子发表成功，请耐心等待审核');
            }
            $thread->body = app('parser.at')->parse($thread->body);
            $thread->body = app('parser.emotion')->parse($thread->body);
            $thread->save();
            $threadBll->threadPassAutoAudit($thread);
            return Redirect::route('thread.show', ['thread' => $thread->id])
                ->withSuccess('帖子审核通过，发表成功！');

        } catch (\Exception $e) {
                return Redirect::route('thread.create')
                    ->withInput(Input::all())
                    ->withErrors($e->getMessageBag());
            }

    }

    /**
     * Shows the edit thread view.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\View\View
     */
    public function edit(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);
        $sections = Section::orderBy('order')->get();

        $thread->body = $thread->body_original;

        return $this->view('threads.create_edit')
            ->withThread($thread)
            ->withSections($sections)
            ->withNode($thread->node);
    }

    /**
     * Creates a new append.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function append(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);

        $content = Input::get('content') ?: '';

        try {
            dispatch(new AddAppendCommand(
                $thread->id,
                $content
            ));
        } catch (ValidationException $e) {
            return Redirect::route('thread.show', $thread->id)
                ->withInput(Input::all())
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('thread.show', $thread->id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    /**
     * Edit a thread.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Thread $thread)
    {
        $threadData = Input::get('thread');

        $this->needAuthorOrAdminPermission($thread->user_id);

        try {
            dispatch(new UpdateThreadCommand($thread, $threadData));
        } catch (ValidationException $e) {
            return Redirect::route('thread.edit', $thread->id)
                ->withInput(Input::all())
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('thread.show', $thread->id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    /**
     * Recommend a thread.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\View\View
     */
    public function excellent(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);

        $updateData = [
            'is_excellent' => !$thread->is_excellent,
        ];

        $thread = dispatch(new UpdateThreadCommand($thread, $updateData));
        if ($thread->is_excellent == 1) {
            event(new ExcellentWasAddedEvent($thread->user));
        }

        return Redirect::route('thread.show', $thread->id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function pin(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);
        if($thread->order > 0){
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '取消置顶');
        }else{
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '置顶');
            event(new PinWasAddedEvent($thread->user, 'Thread'));
            event(new ThreadWasPinnedEvent($thread));
        }
        return Redirect::route('thread.show', $thread->id);
    }

    public function sink(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);

        if ($thread->order < 0) {
            $thread->increment('order', 1);
            $this->updateOpLog($thread, '取消下沉');
        }else{
            $thread->decrement('order', 1);
            $this->updateOpLog($thread, '下沉');
            event(new SinkWasAddedEvent($thread->user));
        }

        return Redirect::route('thread.show', $thread->id);
    }

    /**
     * Deletes a given thread.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);

        dispatch(new RemoveThreadCommand($thread));

        return Redirect::route('thread.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function postTrash(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);

        DB::beginTransaction();
        try {
            $thread->status = -1;
            $thread->node->update(['thread_count' => $thread->node->threads()->visible()->count()]);
            $thread->user->update(['thread_count' => $thread->user->threads()->visible()->count()]);
            $this->updateOpLog($thread, '删除帖子', trim(request('reason')));
            DB::commit();
        } catch (ValidationException $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessageBag());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }
}
