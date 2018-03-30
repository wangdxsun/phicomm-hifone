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
        $version = get_app_version();
        //过滤当前APP版本和Banner设定区间匹配者
        if (Agent::match('PhiWifiNative') && Agent::match('iPhone')) {
            $carousels = Carousel::orderBy('order')
                ->whereIn('device', [Carousel::IOS, Carousel::APP])->visible()->get();
        } elseif (Agent::match('PhiWifiNative') && Agent::match('Android')) {
            $carousels = Carousel::orderBy('order')
                ->whereIn('device', [Carousel::ANDROID, Carousel::APP])->visible()->get();
        } else {
            return [];
        }
        $carouselsFiltered = $carousels->filter(function ($carousel) use ($version) {
            return $this->compareVersion($version, $carousel);
        });
        $carousels = $carouselsFiltered->values();

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

    private function compareVersion($version, $carousel)
    {
        if ($carousel->start_version == '全部版本') {
            return true;
        }
        $versionArr = explode('.', $version);
        $versionArrStart = explode('.', $carousel->start_version);
        $versionArrEnd = explode('.', $carousel->end_version);
        return $this->compare($versionArrStart, $versionArr) &&
            $this->compare($versionArr,$versionArrEnd);
    }

    /**
     * 比较版本号
     * 如果$versionA <= $versionB return true
     * 如果$versionA > $versionB return false
     */
    private function compare($versionA, $versionB) {
        for ($i = 0; $i < 3; $i++) {
            if (intval($versionA[$i]) < intval($versionB[$i])) {
                return true;
            } elseif (intval($versionA[$i]) == intval($versionB[$i])) {
                continue;
            } else {
                return false;
            }
        }
        return true;
    }

}