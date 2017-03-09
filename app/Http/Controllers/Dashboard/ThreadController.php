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

use Hifone\Commands\Thread\RemoveThreadCommand;
use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\Thread;
use Hifone\Parsers\Markdown;
use Illuminate\Support\Facades\View;
use Input;
use Redirect;

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
            'sub_title'    => trans_choice('dashboard.threads.threads', 2),
            'sub_header'   => '帖子管理'
        ]);
    }

    public function index()
    {
        $q = Input::query('q');
        $threads = Thread::visible()->search($q)->orderBy('order', 'desc')->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.threads.index')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withCurrentMenu('index');
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
            ->withPageTitle(trans('dashboard.threads.edit.title').' - '.trans('dashboard.dashboard'))
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
            $thread = dispatch(new UpdateThreadCommand($thread, $threadData));
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.thread.edit', $thread->id)
                ->withInput($threadData)
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.thread.edit', ['id' => $thread->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.threads.edit.success')));
    }

    public function pin(Thread $thread)
    {
        ($thread->order > 0) ? $thread->decrement('order', 1) : $thread->increment('order', 1);

        return Redirect::route('dashboard.thread.index')
            ->withSuccess(trans('dashboard.threads.edit.success'));
    }

    public function excellent(Thread $thread)
    {
        ($thread->is_excellent > 0) ? $thread->decrement('is_excellent', 1) : $thread->increment('is_excellent', 1);

        return Redirect::route('dashboard.thread.index')
            ->withSuccess(trans('dashboard.threads.edit.success'));
    }

    public function destroy(Thread $thread)
    {
        dispatch(new RemoveThreadCommand($thread));

        return Redirect::route('dashboard.thread.trash')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function audit()
    {
        $q = Input::query('q');
        $threads = Thread::audit()->search($q)->orderBy('order', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        return view('dashboard.threads.audit')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withCurrentMenu('audit');
    }

    public function postAudit(Thread $thread)
    {
        $thread->order = 0;
        $thread->save();
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function trash()
    {
        $q = Input::query('q');
        $threads = Thread::trash()->search($q)->orderBy('order', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        return view('dashboard.threads.trash')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withCurrentMenu('trash');
    }

    public function postTrash(Thread $thread)
    {
        $thread->order = -1;
        $thread->save();
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
