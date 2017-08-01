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
use Hifone\Services\Filter\Utils\TrieTree;
use Hifone\Services\Filter\WordInit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Request;
use Input;
use DB;

class WordController extends Controller
{
    private $trieTree;

    /**
     * Creates a new node controller instance.
     *
     */
    public function __construct(TrieTree $trieTree)
    {
        $this->trieTree = $trieTree;
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
        $search = array_filter(Input::get('word', []), function($value) {
            return !empty($value);
        });
        $types = Word::$types;
        $statuses = Word::$statuses;
        $words = Word::orderBy('created_at', 'desc')->search($search)->paginate(20);
        $wordCount = Word::search($search)->count();
        return View::make('dashboard.words.index')
            ->withPageTitle(trans('dashboard.words.word'))
            ->with('statuses',$statuses)
            ->withTypes($types)
            ->withSearch($search)
            ->withWords($words)
            ->withWordCount($wordCount);
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
        $wordData['created_at'] = date('Y-m-d H:i:s');

        //判断重复敏感词，转入编辑
        $oldWord = Word::where('word',$wordData['word'])->first();
        if ($oldWord) {
            return Redirect::route('dashboard.word.edit', $oldWord->id)
                ->withErrors('当前添加的敏感词重复，请重新编辑！');
        }
        try {
            $word = Word::create($wordData);
            $this->cacheInsert($this->trieTree, $word->word);
            $this->updateOpLog($word, "添加敏感词");
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

    public function update()
    {
        $wordData = request('word');
        $word = Word::find($wordData['id']);
        $beforeWord = $word->word;

        //判断重复敏感词，提示重新编辑
        if ($beforeWord != $wordData['word']) {//修改词语本身
            $afterWord = Word::where('word',$wordData['word'])->first();
            if ($afterWord) {
                return Redirect::back()->withErrors('修改后敏感词重复，请重新编辑！');
            }
        }
        unset($wordData['id']);
        $word->update($wordData);
        //更新缓存
        if ($beforeWord != $wordData['word']) {
            $this->cacheReplace($this->trieTree, $beforeWord, $wordData['word']);
        }
        $this->updateOpLog($word, '修改敏感词');

        return Redirect::route('dashboard.word.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.words.edit.success')));
    }

    //批量删除敏感词
    public function batchDestroy(){
        $count = 0;
        $word_ids = Input::get('batch');
        if ($word_ids != null) {
            DB::beginTransaction();
            try {
                foreach ($word_ids as $id) {
                    if ($word = Word::find($id)){
                        $this->updateOpLog($word,"删除敏感词");
                        $word->delete();
                        $count++;
                    }
                }
                DB::commit();
            } catch (ValidationException $e) {
                DB::rollBack();
                return Redirect::back()->withErrors($e->getMessageBag());
            }
            $this->cacheAll($this->trieTree);
            return Redirect::back()->withSuccess('恭喜，批量删除成功！'.'共'.$count.'条');
        } else {
            return Redirect::back()->withErrors('您未选中任何记录！');
        }

    }

    public function destroy(Word $word)
    {
        $this->updateOpLog($word,"删除敏感词");
        $this->cacheRemove($this->trieTree, $word->word);
        $word->delete();
        return Redirect::route('dashboard.word.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    protected function cacheAll(TrieTree $trieTree)
    {
        $cacheTime = 30 * 24 * 60; // 单位为分钟
        $words = Word::pluck('word');
        $tree = $trieTree->importBadWords($words);
        Cache::put('words', $tree, $cacheTime);
    }

    protected function cacheInsert(TrieTree $trieTree, $word)
    {
        $this->initTree($trieTree);
        
        $cacheTime = 30 * 24 * 60; // 单位为分钟
        $newTree = $trieTree->insert($word);
        Cache::put('words', $newTree, $cacheTime);
    }

    protected function cacheReplace(TrieTree $trieTree, $beforeWord, $afterWord)
    {
        $cacheTime = 30 * 24 * 60; // 单位为分钟

        $this->initTree($trieTree);
        $trieTree->tree = $trieTree->remove($beforeWord);
        $newTree = $trieTree->insert($afterWord);
        Cache::put('words', $newTree, $cacheTime);
    }

    protected function cacheRemove(TrieTree $trieTree, $word)
    {
        $cacheTime = 30 * 24 * 60; // 单位为分钟

        $this->initTree($trieTree);
        $newTree = $trieTree->remove($word);
        Cache::put('words', $newTree, $cacheTime);
    }

    protected function initTree(TrieTree $trieTree) {
        $tree = Cache::get('words', []);
        $trieTree->tree = $tree;
    }

}
