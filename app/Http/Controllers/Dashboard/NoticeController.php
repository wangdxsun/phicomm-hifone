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
use Hifone\Models\Notice;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class NoticeController extends Controller
{
    /**
     * Creates a new notice controller instance.

     *
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'notices',
            'sub_title'     => trans_choice('dashboard.notices.notices', 2),
        ]);
    }
    /**
     * Shows the notices view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notices = Notice::all();

        return View::make('dashboard.notice.index')
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
        return View::make('dashboard.notice.create_edit')
            ->withPageTitle(trans('dashboard.notices.notice').' - '.trans('dashboard.notices.add.title'));
    }

    /**
     * Stores a new notice.
     * @param  Request  $request
     * @return Response
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $noticeData = Request::get('notice');

        try {
            Notice::create($noticeData);
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.notice.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.notice.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.add.success')));
    }

    public function edit(User $user)
    {
        $this->subMenu['users']['active'] = true;

        return View::make('dashboard.users.create_edit')
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'))
            ->withUser($user)
            ->withSubMenu($this->subMenu);
    }

    public function update(User $user)
    {
        $userData = Input::get('user');
        if ($userData['password']) {
            $userData['salt'] = str_random(6);
            $userData['password'] = $this->hasher->make($userData['password'], ['salt' => $userData['salt']]);
        } else {
            unset($userData['password']);
        }
        try {
            $user->update($userData);
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.user.edit', ['id' => $user->id])
                ->withInput(Input::except('password'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.users.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.user.edit', ['id' => $user->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.edit.success')));
    }
}
