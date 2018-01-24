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
        if (Agent::match('PhiWifiNative') && Agent::match('iPhone')) {
            $carousels = Carousel::orderBy('order')
                ->whereIn('device', [Carousel::IOS, Carousel::IOS + Carousel::ANDROID])->visible()->get();
        } elseif (Agent::match('PhiWifiNative') && Agent::match('Android')) {
            $carousels = Carousel::orderBy('order')
                ->whereIn('device', [Carousel::ANDROID, Carousel::ANDROID + Carousel::IOS])->visible()->get();
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

    public function compareVersion($version, $carousel)
    {
        if ($carousel->start_version == '全部版本') {
            return true;
        }
        $versionArr = explode('.', $version);
        $targetVersionArrStart = explode('.', $carousel->start_version);
        $targetVersionArrEnd = explode('.', $carousel->end_version);
        return $this->compare($targetVersionArrEnd, $versionArr,0) &&
            $this->compare($versionArr,$targetVersionArrStart, 0);
    }

    public function compare($versionArr, $targetVersionArr, $i)
    {
        if ($i > count($versionArr) - 1 ) {
            return true;
        }
        if (intval($versionArr[$i]) > intval($targetVersionArr[$i])) {
            return true;
        } elseif (intval($versionArr[$i]) < intval($targetVersionArr[$i])) {
            return false;
        } else {
            return $this->compare($versionArr, $targetVersionArr, ++$i);
        }
    }

}