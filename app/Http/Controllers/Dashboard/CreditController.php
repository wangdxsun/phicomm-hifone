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
use Hifone\Hashing\PasswordHasher;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Credit\Rule;
use Hifone\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Input;

class CreditController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;

        View::share([
            'current_menu'  => 'credit',
            'sub_title'     => '积分管理',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $rules = Rule::all();

        return View::make('dashboard.credit.index')
            ->withPageTitle('积分管理')
            ->withRules($rules);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.roles.create_edit')
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'));
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $userData = Input::get('user');
        $userData['salt'] = str_random(16);
        $userData['password'] = $this->hasher->make($userData['password'], ['salt' => $userData['salt']]);

        try {
            User::create($userData);
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.user.create')
                ->withInput(Input::get('user'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.users.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.user.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.add.success')));
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
