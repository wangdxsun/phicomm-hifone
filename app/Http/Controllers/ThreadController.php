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
use Hifone\Commands\Thread\AddThreadCommand;
use Hifone\Commands\Thread\RemoveThreadCommand;
use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Events\Thread\ThreadWasViewedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Repositories\Criteria\Thread\BelongsToNode;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use Illuminate\Support\Facades\Config;
use Input;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadController extends Controller
{
    /**
     * Creates a new thread controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Shows the threads view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $repository = app('repository');
        $repository->pushCriteria(new Filter(Input::query('filter')));
        $repository->pushCriteria(new Search(Input::query('q')));

        $threads = $repository->model(Thread::class)->getThreadList(Config::get('setting.threads_per_page', 15));

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

        $replies = $thread->replies()->visible()
            ->orderBy('id', 'asc')
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
        $node = Node::find(Input::query('node_id'));
        $sections = Section::orderBy('order')->get();

        $this->breadcrumb->push(trans('hifone.threads.add'), route('thread.create'));

        return $this->view('threads.create_edit')
            ->withSections($sections)
            ->withNode($node);
    }

    /**
     * Creates a new node.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        if (Auth::user()->hasRole('NoComment')) {
            return Redirect::back()->withErrors('您已被系统管理员禁言');
        }
        $threadData = Input::get('thread');
        $node_id = isset($threadData['node_id']) ? $threadData['node_id'] : null;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';

        try {
            $thread = dispatch(new AddThreadCommand(
                $threadData['title'],
                $threadData['body'],
                Auth::user()->id,
                $node_id,
                $tags
            ));
        } catch (ValidationException $e) {
            return Redirect::route('thread.create')
                ->withInput(Input::all())
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('thread.show', [$thread->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
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
            $append = dispatch(new AddAppendCommand(
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
            $thread = dispatch(new UpdateThreadCommand($thread, $threadData));
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
        if($thread->is_excellent == 1){
            $user = User::find($thread->user_id);
            event(new ExcellentWasAddedEvent($user));
        }

        return Redirect::route('thread.show', $thread->id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    /**
     * Pin a thread.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\View\View
     */
    public function pin(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);
        if($thread->order > 0){
            $thread->decrement('order', 1);
        }else{
            $thread->increment('order', 1);
            $user = User::find($thread->user_id);
            event(new PinWasAddedEvent($user, 'Thread'));
        }
        return Redirect::route('thread.show', $thread->id);
    }

    /**
     * Sink a thread.
     *
     * @param \Hifone\Models\Thread $thread
     *
     * @return \Illuminate\View\View
     */
    public function sink(Thread $thread)
    {
        $this->needAuthorOrAdminPermission($thread->user_id);

        if($thread->order < 0){
            $thread->increment('order', 1);
        }else{
            $thread->decrement('order', 1);
            $user = User::find($thread->user_id);
            event(new SinkWasAddedEvent($user));
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
}
