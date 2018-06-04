<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\TagType;
use View;
use Redirect;
use Input;

class TagTypeController extends Controller
{
    //标签分类
    public function tagType($system)
    {
        if ($system == 'user') {
            $tagTypes = TagType::ofType([TagType::USER, TagType::AUTO])->with('tags')->get();
            return View::make('dashboard.tagTypes.index')
                ->with('tagTypes', $tagTypes)
                ->withCurrentMenu('userTagType');
        } elseif ($system == 'question') {
            $tagTypes = TagType::ofType([TagType::QUESTION])->with('tags')->orderBy('order')->get();
            return View::make('dashboard.questionTagTypes.index')
                ->with('tagTypes', $tagTypes)
                ->withCurrentMenu('questionTagType');
        }
    }

    //新增标签分类
    public function create($system)
    {
        if ($system == 'user') {
            return View::make('dashboard.tagTypes.create_edit')
                ->withCurrentMenu('userTagType');
        } elseif ($system == 'question') {
            return View::make('dashboard.questionTagTypes.create_edit')
                ->withCurrentMenu('questionTagType');
        }

    }

    //编辑标签分类
    public function edit(TagType $tagType, $system)
    {
        $types = array_get(TagType::$types[$tagType->type], 'display_name');
        if ($system == 'user') {
            return View::make('dashboard.tagTypes.create_edit')
                ->with('tagType', $tagType)
                ->with('types', json_encode($types))
                ->withCurrentMenu('userTagType');
        } elseif ($system == 'question') {
            return View::make('dashboard.questionTagTypes.create_edit')
                ->with('tagType', $tagType)
                ->with('types', json_encode($types))
                ->withCurrentMenu('questionTagType');
        }
    }

    public function update(TagType $tagType, $system)
    {
        $this->validate(request(),[
            'tagType.display_name'         => 'required|max:5',
        ], [
            'tagType.display_name.required'     => '需填入1-5个字符',
            'tagType.display_name.max'     => '需填入1-5个字符',

        ]);
        $tagTypeData = Input::get('tagType');
        if ($system == 'user') {
            if ($tagType->display_name != array_get($tagTypeData, 'display_name') && null != TagType::where('display_name', array_get($tagTypeData, 'display_name'))->first()) {
                return Redirect::back()
                    ->withErrors('标签分类名已存在');
            }
            $tagTypeData['type'] = 1;
            $tagType->update($tagTypeData);
            return Redirect::route('dashboard.tag.type', ['user'])
                ->withSuccess('标签分类已更新');
        } elseif ($system == 'question') {
            if ($tagType->display_name != array_get($tagTypeData, 'display_name') && null != TagType::where('display_name', array_get($tagTypeData, 'display_name'))->first()) {
                return Redirect::back()
                    ->withErrors('问题分类名已存在');
            }
            $tagTypeData['type'] = 2;
            $tagType->update($tagTypeData);
            return Redirect::route('dashboard.question.tag.type', ['question'])
                ->withSuccess('问题分类已更新');
        }
    }

    public function store($system)
    {
        $this->validate(request(),[
            'tagType.display_name'         => 'required|max:5',
        ], [
            'tagType.display_name.required'     => '需填入1-5个字符',
            'tagType.display_name.max'     => '需填入1-5个字符',

        ]);
        $tagTypeData = Input::get('tagType');
        if ($system == 'user') {
            if (null != TagType::where('display_name', array_get($tagTypeData, 'display_name'))->first()) {
                return Redirect::back()
                    ->withErrors('标签分类名已存在');
            }
            $tagTypeData['type'] = 1;
            TagType::create($tagTypeData);
            return Redirect::route('dashboard.tag.type', ['user'])
                ->withSuccess('标签分类已新增');
        } elseif ($system == 'question') {
            if (null != TagType::where('display_name', array_get($tagTypeData, 'display_name'))->first()) {
                return Redirect::back()
                    ->withErrors('问题分类名已存在');
            }
            $tagTypeData['type'] = 2;
            TagType::create($tagTypeData);
            return Redirect::route('dashboard.question.tag.type', ['question'])
                ->withSuccess('问题分类已更新');
        }

    }

    //删除标签分类，同时删除分类下的所有标签
    public function destroy(TagType $tagType, $system)
    {
        $tags = $tagType->tags;
        foreach ($tags as $tag) {
            $tag->delete();
        }
        $tagType->delete();
        if($system == 'user') {
            return Redirect::back()->withSuccess('已删除该标签分类及分类下所有标签！');
        } elseif ($system == 'question') {
            return Redirect::back()->withSuccess('已删除该问题分类及分类下所有子类！');
        }

    }
}