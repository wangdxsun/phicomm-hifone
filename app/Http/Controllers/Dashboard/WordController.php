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
use Hifone\Models\Word;
use Hifone\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Input;

class WordController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'words',
            'sub_title'     => trans_choice('dashboard.words.words', 2),
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $q = Input::query('q');
        $words  = Word::orderBy('created_at', 'desc')->search($q)->paginate(5);
        return View::make('dashboard.words.index')
            ->withPageTitle(trans('dashboard.words.word'))
            ->withWords($words);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.words.create_edit')
            ->withPageTitle(trans('dashboard.words.word').' - '.trans('dashboard.words.add.head_title'));
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $wordData = Request::get('word');
        //dd(var_dump($wordData));
        try {
            Word::create($wordData);
        } catch (ValidationException $e) {

            /*$errorMsg = json_decode( json_encode( $e->getMessageBag()),true);
            $errorMsg['content'][0] = '内容不能为空';*/

            return Redirect::route('dashboard.word.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.words.add.failure')))
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.word.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.words.add.success')));
    }

    public function edit(Word $word)
    {

        return View::make('dashboard.words.create_edit')
            ->withPageTitle(trans('dashboard.words.word').' - '.trans('dashboard.words.edit.head_title'))
            ->withWord($word);
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
