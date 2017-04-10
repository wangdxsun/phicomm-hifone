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
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Section;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Services\Parsers\Markdown;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
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
            'sub_title'    => trans_choice('dashboard.threads.threads', 2),
            'sub_header'   => '帖子管理'
        ]);
    }

    public function index()
    {
        $search = array_filter(Input::get('thread', []), function($value) {
            return !empty($value);
        });
        $threads = Thread::visible()->search($search)->with('node', 'user', 'lastOpUser')->orderBy('created_at', 'desc')->paginate(20);
        $threadAll = Thread::visible()->get()->toArray();
        $threadIds = array_unique(array_column($threadAll, 'user_id'));
        $sections = Section::orderBy('order')->get();

        return View::make('dashboard.threads.index')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withCurrentMenu('index')
            ->withSections($sections)
            ->withUsers(User::find($threadIds));
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

    public function sink(Thread $thread)
    {
        ($thread->order >= 0) ? $thread->decrement('order', 1) : $thread->increment('order', 1);

        return Redirect::route('dashboard.thread.index')
            ->withSuccess(trans('dashboard.threads.edit.success'));
    }

    public function excellent(Thread $thread)
    {
        ($thread->is_excellent > 0) ? $thread->decrement('is_excellent', 1) : $thread->increment('is_excellent', 1);

        return Redirect::back()
            ->withSuccess(trans('dashboard.threads.edit.success'));
    }

    public function destroy(Thread $thread)
    {
        dispatch(new RemoveThreadCommand($thread));

        return Redirect::route('dashboard.thread.trash')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
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

    public function postAudit(Thread $thread)
    {
        $thread->status = 0;
        $thread->save();
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function trash()
    {
        $search = array_filter(Input::get('thread', []), function($value) {
            return !empty($value);
        });
        $threads = Thread::trash()->search($search)->with('node', 'user', 'lastOpUser')->orderBy('created_at', 'desc')->paginate(20);
        $threadAll = Thread::trash()->get()->toArray();
        $userIds = array_unique(array_column($threadAll, 'user_id'));
        $operators = array_unique(array_column($threadAll, 'last_op_user_id'));
        $sections = Section::orderBy('order')->get();

        return view('dashboard.threads.trash')
            ->withPageTitle(trans('dashboard.threads.threads').' - '.trans('dashboard.dashboard'))
            ->withThreads($threads)
            ->withSections($sections)
            ->withCurrentMenu('trash')
            ->withUsers(User::find($userIds))
            ->withOperators(User::find($operators));
    }

    public function postTrash(Thread $thread)
    {
        $thread->status = -1;
        $thread->last_op_reason = Input::get('reason');
        $thread->save();
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
