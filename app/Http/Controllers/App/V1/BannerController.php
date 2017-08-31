<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 15:42
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Events\Banner\BannerWasViewedEvent;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Carousel;

class BannerController extends AppController
{
    public function index()
    {
        return Carousel::visible()->orderBy('order')->get();
    }

    public function show(Carousel $carousel)
    {
        event(new BannerWasViewedEvent($carousel));
    }
}