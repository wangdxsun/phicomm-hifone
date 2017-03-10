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
use Hifone\Http\Controllers\Controller;
use Hifone\Hashing\PasswordHasher;
use Hifone\Models\Notice;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Input;


class NoticeController extends Controller
{
    /**
     * Creates a new notice controller instance.

     *
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
        View::share([
            'current_menu'  => 'nodes',
            'sub_title'     => trans_choice('dashboard.nodes.nodes', 2),
        ]);
    }
    /**
     * Shows the notices view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $q = Input::query('q');
        $notices  = Notice::orderBy('created_at', 'desc')->search($q)->paginate(5);
        return View::make('dashboard.notices.index')
            ->withPageTitle(trans('dashboard.notices.notice'))
            ->withNotices($notices);
    }

    /**
     * Shows the create notice view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.notices.create_edit')
            ->withPageTitle(trans('dashboard.notices.notice').' - '.trans('dashboard.notices.add.title'));
    }

    /**
     * Stores a new notice.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {

        $noticeData = Request::get('notice');
        try {
            Notice::create($noticeData);
        } catch (ValidationException $e) {

            $errorMsg = json_decode( json_encode( $e->getMessageBag()),true);
            $errorMsg['content'][0] = '内容不能为空';

            return Redirect::route('dashboard.notice.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.add.failure')))
                ->withErrors($errorMsg);
        }

        return Redirect::route('dashboard.notice.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.add.success')));
    }

    public function edit(Notice $notice)
    {
        return View::make('dashboard.notices.create_edit')
            ->withPageTitle(trans('dashboard.notices.notice').' - '.trans('dashboard.notices.edit.title'))
            ->withNotice($notice);
    }

    public function update(Notice $notice)
    {
        $noticeData = Request::get('notice');
        try {
            $notice->update($noticeData);
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.notice.edit', ['id' => $notice->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.notice.index', ['id' => $notice->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.edit.success')));
    }
    public function destroy(Notice $notice)
    {
        $notice->delete();;

        return Redirect::route('dashboard.notice.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}