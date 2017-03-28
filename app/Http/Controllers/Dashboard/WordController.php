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
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Input;
use DB;

class WordController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
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
        $search = array_filter(Input::get('query', []), function($value) {
            return !empty($value);
        });
        $words = Word::orderBy('created_at', 'desc')->search($search)->paginate(5);
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
        $wordData['admin'] = '管理员';
        $wordData['created_at'] = date('Y-m-d H:i:s');
        try {
            Word::create($wordData);
        } catch (ValidationException $e) {

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
    public function editInfo(Request $request)
    {
        $input = Request::all();
        $word = $input['word'];

        if(!empty($word['find']) && !empty($word['type']) && !empty($word['replacement'])){
             DB::update('update words set find=?,type=?,replacement=?,substitute=? where id=?', array($word['find'],
                $word['type'],$word['replacement'],$word['substitute'],$word['id']));
            return Redirect::back();
        }else{
            $errorMsg['content'][0] = '必填项不能为空';
            return Redirect::back()
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.words.edit.failure')))
                ->withErrors($errorMsg);
        }
    }


    public function update(Word $word)
    {
    }
    public function destroy(Word $word)
    {
        $word->delete();
        return Redirect::route('dashboard.word.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
