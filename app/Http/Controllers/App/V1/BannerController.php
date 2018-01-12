<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 15:42
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Events\Banner\BannerWasViewedEvent;
use Hifone\Http\Bll\BannerBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Carousel;
use Jenssegers\Agent\Facades\Agent;

class BannerController extends AppController
{
    public function index()
    {
        if (Agent::is('iPhone')) {
            $carousels = Carousel::orderBy('order')->whereIn('device', [8,12])->visible()->get();
        } elseif (Agent::is('Android')) {
            $carousels = Carousel::orderBy('order')->whereIn('device', [4,12])->visible()->get();
        } else {
            return null;
        }

        foreach ($carousels as $carousel) {
            $carousel['statistic'] = route('app.banner.show', $carousel->id);
        }
        return $carousels;
    }

    public function bannerViewCount(Carousel $carousel, BannerBll $bannerBll)
    {
        event(new BannerWasViewedEvent($carousel));
        $bannerBll->appUpdateActiveTime();
        return '统计banner点击';
    }
}