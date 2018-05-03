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

    //查询所有的用户标签
    public function user()
    {
        $tags = Tag::whereIn('tag_type_id', TagType::ofType([TagType::USER])->pluck('id'))->with('tagType')->get();
        return View::make('dashboard.tags.index')
            ->with('tags', $tags)
            ->withCurrentMenu('tag');

    }

    //查询所有的问题标签
    public function question()
    {
        $tags = Tag::whereIn('tag_type_id', TagType::ofType([TagType::QUESTION])->pluck('id'))->with('tagType')->get();
        return View::make('dashboard.tags.index')
            ->with('tags', $tags)
            ->withCurrentMenu('tag');

    }

    public function create()
    {
        $tagTypes = TagType::ofType([TagType::USER])->get();
        return View::make('dashboard.tags.create_edit')
            ->with('tagTypes', $tagTypes)
            ->withCurrentMenu('tag');

    }

    public function edit(Tag $tag)
    {
        return View::make('dashboard.tags.create_edit')
            ->with('tag', $tag)
            ->with('tagTypes', TagType::ofType([TagType::USER])->get())
            ->withCurrentMenu('tag');

    }

    public function update(Tag $tag)
    {
        $tagData = Input::get('tag');
        if ($tag->name != array_get($tagData, 'name') && null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            return Redirect::back()
                ->withErrors('标签名已存在');
        }
        $tag->update($tagData);
        return Redirect::route('dashboard.tag.index')
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
        return Redirect::route('dashboard.tag.index')
            ->withSuccess('标签已新增');
    }

    //删除标签
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return Redirect::back()->withSuccess('已删除该标签！');
    }
}