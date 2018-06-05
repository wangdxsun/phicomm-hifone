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

    //查询标签(用户含自动标签/问题分类)
    public function index($system)
    {
        if ($system == 'user') {
            $tags = Tag::whereIn('tag_type_id', TagType::ofType([TagType::USER])->pluck('id'))->orWhere('channel', '=', 0)->with('tagType')->get();
            return View::make('dashboard.tags.index')
                ->with('tags', $tags)
                ->withCurrentMenu('userTag');
        } elseif ($system == 'question') {
            $tags = Tag::whereIn('tag_type_id', TagType::ofType([TagType::QUESTION])->pluck('id'))->with('tagType')->orderBy('order')->get();
            return View::make('dashboard.questionTags.index')
                ->with('tags', $tags)
                ->withCurrentMenu('questionTag');
        }

    }

    //新增标签
    public function create($system)
    {
        if ($system == 'user') {
            $tagTypes = TagType::ofType([TagType::USER])->get();
            return View::make('dashboard.tags.create_edit')
                ->with('tagTypes', $tagTypes)
                ->withCurrentMenu('userTag');
        } elseif ($system == 'question') {
            $tagTypes = TagType::ofType([TagType::QUESTION])->get();
            return View::make('dashboard.questionTags.create_edit')
                ->with('tagTypes', $tagTypes)
                ->withCurrentMenu('questionTag');
        }


    }

    //编辑标签
    public function edit(Tag $tag, $system)
    {
        if ($system == 'user') {
            return View::make('dashboard.tags.create_edit')
                ->with('tag', $tag)
                ->with('tagTypes', TagType::ofType([TagType::USER])->get())
                ->withCurrentMenu('userTag');
        } elseif ($system == 'question') {
            return View::make('dashboard.questionTags.create_edit')
                ->with('tag', $tag)
                ->with('tagTypes', TagType::ofType([TagType::QUESTION])->get())
                ->withCurrentMenu('questionTag');
        }


    }

    //更新标签
    public function update(Tag $tag, $system)
    {
        $this->validate(request(),[
            'tag.name'         => 'required|max:5',
        ], [
            'tag.name.required'     => '需填入1-5个字符',
            'tag.name.max'     => '需填入1-5个字符',

        ]);
        $tagData = Input::get('tag');

        if ($tag->name != array_get($tagData, 'name') && null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            if ($system == 'user') {
                return Redirect::back()
                    ->withErrors('标签名已存在');
            } elseif ($system == 'question') {
                return Redirect::back()
                    ->withErrors('子类名已存在');
            }

        }
        $tag->update($tagData);
        if ($system == 'user') {
            return Redirect::route('dashboard.tag', ['user'])
                ->withSuccess('用户标签已更新');
        } elseif ($system == 'question') {
            return Redirect::route('dashboard.question.tag', ['question'])
                ->withSuccess('问题子类已更新');
        }


    }

    //保存标签
    public function store($system)
    {
        $this->validate(request(),[
            'tag.name'                => 'required|max:5',
            'tag.tag_type_id'         => 'required',
        ], [
            'tag.name.required'         => '需填入1-5个字符',
            'tag.name.max'              => '需填入1-5个字符',
            'tag.tag_type_id.required'  => '暂无问题分类，请新建问题分类后添加问题子类',

        ]);
        $tagData = Input::get('tag');

        if (null != Tag::where('name', array_get($tagData, 'name'))->first()) {
            if ($system == 'user') {
                return Redirect::back()
                    ->withErrors('标签名已存在');
            } elseif ($system == 'question') {
                return Redirect::back()
                    ->withErrors('子类名已存在');
            }
        }
        Tag::create($tagData);
        if ($system == 'user') {
            return Redirect::route('dashboard.tag', ['user'])
                ->withSuccess('用户标签已新增');
        } elseif ($system == 'question') {
            return Redirect::route('dashboard.question.tag', ['question'])
                ->withSuccess('问题子类已新增');
        }
    }


    //删除标签
    public function destroy(Tag $tag, $system)
    {

        if ($system == 'user') {
            $tag->delete();
            return Redirect::back()->withSuccess('已删除该标签！');
        } elseif ($system == 'question') {
            if ($tag->questions->count() > 0) {
                return Redirect::back()->withErrors('该分类下有问答内容，不可删除！');
            } else {
                return Redirect::back()->withSuccess('已删除该问题子类！');
            }
        }
    }

}