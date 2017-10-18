<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/25
 * Time: 13:49
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Events\Banner\BannerWasViewedEvent;
use Hifone\Models\Carousel;

class BannerController extends ApiController
{
    public function index()
    {
        $carousels = Carousel::visible()->orderBy('order')->get();
        foreach ($carousels as $carousel) {
            $carousel['statistic'] = route('api.banner.show', $carousel->id);
        }
        return $carousels;
    }

    public function show(Carousel $carousel)
    {
        //统计Banner次数
        event(new BannerWasViewedEvent($carousel));
        if ($carousel->type == 0) {
            return redirect($carousel->url);
        } else {
            return redirect('/thread/'.$carousel->url);
        }
    }
}