<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/25
 * Time: 13:49
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Models\Carousel;

class BannerController extends ApiController
{
    public function index()
    {
        return Carousel::visible()->orderBy('order')->get();
    }
}