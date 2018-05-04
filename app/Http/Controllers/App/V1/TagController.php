<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 14:03
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Tag;
use Hifone\Models\TagType;

class TagController extends AppController
{
    public function tagTypes()
    {
        $tagTypes = TagType::ofType(TagType::QUESTION)->with(['tags'])->get();

        return $tagTypes;
    }
}