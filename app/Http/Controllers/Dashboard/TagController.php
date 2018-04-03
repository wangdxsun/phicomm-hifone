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

    public function index()
    {
        //所有的用户标签
        $tags = Tag::whereIn('type', TagType::ofType(TagType::USER)->pluck('id'))->get();
        return View::make('dashboard.tags.index')
            ->with('tags', $tags)
            ->withCurrentMenu('tag')
            ->withCurrentNav('user');

    }

    public function user()
    {
        //所有的用户标签
        $tags = Tag::whereIn('type', TagType::ofType(TagType::USER)->pluck('id'))->get();
        return View::make('dashboard.tags.index')
            ->with('tags', $tags)
            ->withCurrentMenu('tag')
            ->withCurrentNav('user');

    }

    public function create()
    {
        $tagTypes = TagType::ofType(TagType::USER)->get();
        return View::make('dashboard.tags.create_edit')
            ->with('tagTypes', $tagTypes)
            ->withCurrentMenu('tag');

    }

    public function edit(Tag $tag)
    {
        return View::make('dashboard.tags.create_edit')
            ->with('tag', $tag)
            ->with('tagTypes', TagType::all())
            ->withCurrentMenu('tag');

    }

    public function update(Tag $tag)
    {
        $tagData = Input::get('tag');
        $tag->update($tagData);
        return Redirect::route('dashboard.tag.index')
            ->withSuccess('标签已更新');
    }

    public function store()
    {
        $tagData = Input::get('tag');
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