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
        $version = parse_agent_version($_SERVER['HTTP_USER_AGENT']);
        //TODO 过滤当前APP版本和Banner设定区间匹配者
//        dd($version > "6.0.0", '6.2.0' > '6.10.0');
        if (Agent::match('PhiWifiNative') && Agent::match('iPhone')) {
            $carousels = Carousel::orderBy('order')->whereIn('device', [Carousel::IOS, Carousel::IOS + Carousel::ANDROID])->visible()->get();
        } elseif (Agent::match('PhiWifiNative') && Agent::match('Android')) {
            $carousels = Carousel::orderBy('order')->whereIn('device', [Carousel::ANDROID, Carousel::ANDROID + Carousel::IOS])->visible()->get();
        } else {
            return [];
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