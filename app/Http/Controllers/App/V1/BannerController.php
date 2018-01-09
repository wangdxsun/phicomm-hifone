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
use Jenssegers\Agent\Facades\Agent;

class BannerController extends AppController
{
    public function index()
    {
        if (Agent::is('iPhone')) {
            $carousels = Carousel::orderBy('order')->whereIn('device', [Carousel::IOS, Carousel::IOS + Carousel::ANDROID])->visible()->get();
        } elseif (Agent::is('Android')) {
            $carousels = Carousel::orderBy('order')->whereIn('device', [Carousel::ANDROID, Carousel::ANDROID + Carousel::IOS])->visible()->get();
        } else {
            return [];
        }

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