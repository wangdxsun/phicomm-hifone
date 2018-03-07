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
        return View::make('dashboard.tags.index')
            ->with('tags', Tag::orderBy('type','desc')->get())
            ->withCurrentMenu('tag');

    }

    public function create()
    {
        $tagTypes = TagType::all();
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