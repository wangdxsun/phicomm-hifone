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
        $carousels = Carousel::visible()->orderBy('order')->get();
        foreach ($carousels as $carousel) {
            $carousel['statistic'] = route('app.banner.show', $carousel->id);
        }
        return $carousels;
    }

    public function bannerViewCount(Carousel $carousel)
    {
        event(new BannerWasViewedEvent($carousel));

        return '统计banner点击';
    }
}