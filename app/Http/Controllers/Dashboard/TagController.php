<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Tag;
use Hifone\Models\TagType;
use View;
use Redirect;
use Input;

class TagController extends Controller
{

    public function __construct()
    {
        View::share([
            'current_menu'  => 'tag',
        ]);
    }

    //查询所有的用户标签(含自动标签)
    public function user()
    {
        $tags = Tag::whereIn('tag_type_id', TagType::ofType([TagType::USER])->pluck('id'))->orWhere('channel', '=', 0)->with('tagType')->get();
        return View::make('dashboard.tags.index')
            ->with('tags', $tags)
            ->withCurrentMenu('userTag');

    }

    //查询所有的问题标签
    public function question()
    {
        $tags = Tag::whereIn('tag_type_id', TagType::ofType([TagType::QUESTION])->pluck('id'))->with('tagType')->get();
        return View::make('dashboard.questionTags.index')
            ->with('tags', $tags)
            ->withCurrentMenu('questionTag');

    }

    //新增用户标签
    public function create()
    {
        $tagTypes = TagType::ofType([TagType::USER])->get();
        return View::make('dashboard.tags.create_edit')
            ->with('tagTypes', $tagTypes)
            ->withCurrentMenu('userTag');

    }

    //新增问题子类
    public function createQuestionTag()
    {
        $tagTypes = TagType::ofType([TagType::QUESTION])->get();
        return View::make('dashboard.questionTags.create_edit')
            ->with('tagTypes', $tagTypes)
            ->withCurrentMenu('questionTag');
    }

    public function edit(Tag $tag)
    {
        return View::make('dashboard.tags.create_edit')
            ->with('tag', $tag)
            ->with('tagTypes', TagType::ofType([TagType::USER])->get())
            ->withCurrentMenu('userTag');

    }

    public function editQuestionTag(Tag $tag)
    {
        return View::make('dashboard.questionTags.create_edit')
            ->with('tag', $tag)
            ->with('tagTypes', TagType::ofType([TagType::QUESTION])->get())
            ->withCurrentMenu('questionTag');

    }

    public function update(Tag $tag)
    {
        $tagData = Input::get('tag');
        if ($tag->name != array_get($tagData, 'name') && null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            return Redirect::back()
                ->withErrors('标签名已存在');
        }
        $tag->update($tagData);
        return Redirect::route('dashboard.tag')
            ->withSuccess('标签已更新');
    }

    public function updateQuestionTag(Tag $tag)
    {
        $tagData = Input::get('tag');
        if ($tag->name != array_get($tagData, 'name') && null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            return Redirect::back()
                ->withErrors('子类名已存在');
        }
        $tag->update($tagData);
        return Redirect::route('dashboard.question.tag')
            ->withSuccess('标签已更新');
    }

    public function store()
    {
        $tagData = Input::get('tag');
        if (null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            return Redirect::back()
                ->withErrors('标签名已存在');
        }
        Tag::create($tagData);
        return Redirect::route('dashboard.tag')
            ->withSuccess('标签已新增');
    }

    public function storeQuestionTag()
    {
        $tagData = Input::get('tag');
        if (null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            return Redirect::back()
                ->withErrors('子类名已存在');
        }
        Tag::create($tagData);
        return Redirect::route('dashboard.question.tag')
            ->withSuccess('标签已新增');

    }

    //删除标签
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return Redirect::back()->withSuccess('已删除该标签！');
    }

    public function destroyQuestionTag(Tag $tag)
    {
        $tag->delete();
        return Redirect::back()->withSuccess('已删除该问题子类！');
    }
}