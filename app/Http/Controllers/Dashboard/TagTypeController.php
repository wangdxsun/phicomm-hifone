<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\TagType;
use View;
use Redirect;
use Input;

class TagTypeController extends Controller
{
    public function index()
    {
        $tagTypes = TagType::all();
        return View::make('dashboard.tagTypes.index')
            ->with('tagTypes', $tagTypes)
            ->withCurrentMenu('index');

    }

    public function create()
    {
        return View::make('dashboard.tagTypes.create_edit')
            ->with('tagTypeTypes', json_encode(TagType::$tagTypeTypes))
            ->withCurrentMenu('index');
    }

    public function edit(TagType $tagType)
    {
        $types = TagType::$tagTypeTypes[$tagType->type]['display_name'];
        return View::make('dashboard.tagTypes.create_edit')
            ->with('tagType', $tagType)
            ->with('types', json_encode($types))
            ->with('tagTypeTypes', json_encode(TagType::$tagTypeTypes))
            ->withCurrentMenu('index');

    }

    public function update(TagType $tagType)
    {
        $tagTypeData = Input::get('tagType');
        $tagType->update($tagTypeData);
        return Redirect::route('dashboard.tag.type.index')
            ->withSuccess('标签分类已更新');
    }

    public function store()
    {
        $tagTypeData = Input::get('tagType');
       TagType::create($tagTypeData);
        return Redirect::route('dashboard.tag.type.index')
            ->withSuccess('标签分类已新增');
    }

    //删除标签分类，同时删除分类下的所有标签
    public function destroy(TagType $tagType)
    {
        $tags = $tagType->tags;
        foreach ($tags as $tag) {
            $tag->delete();
        }
        $tagType->delete();
        return Redirect::back()->withSuccess('已删除该标签分类及分类下所有标签！');
    }
}