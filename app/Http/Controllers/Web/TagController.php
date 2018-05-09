<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 14:03
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Models\Tag;
use Hifone\Models\TagType;

class TagController extends WebController
{
    //问题标签列表
    public function tags()
    {
        $tagTypes = TagType::ofType(TagType::QUESTION)->get();
        $tags = Tag::whereIn('tag_type_id', array_pluck($tagTypes, 'id'))->get();

        return $tags;
    }

    //问题标签列表（含分类）
    public function tagTypes()
    {
        $tagTypes = TagType::ofType(TagType::QUESTION)->with(['tags'])->get();

        return $tagTypes;
    }
}