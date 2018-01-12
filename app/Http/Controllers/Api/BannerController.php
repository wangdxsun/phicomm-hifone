<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/25
 * Time: 13:49
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Events\Banner\BannerWasViewedEvent;
use Hifone\Http\Bll\BannerBll;
use Hifone\Models\Carousel;

class BannerController extends ApiController
{
    public function index()
    {
        $carousels = Carousel::orderBy('order')->whereIn('device', [1,3])->visible()->get();
        foreach ($carousels as $carousel) {
            $carousel['statistic'] = route('api.banner.show', $carousel->id);
        }
        return $carousels;
    }

    public function show(Carousel $carousel, BannerBll $bannerBll)
    {
        //统计Banner次数
        event(new BannerWasViewedEvent($carousel));
        $bannerBll->h5UpdateActiveTime();
        if ($carousel->type == 0) {
            return redirect($carousel->url);
        } else {
            return redirect('/thread/'.$carousel->url);
        }
    }
}