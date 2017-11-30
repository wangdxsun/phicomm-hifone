<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Events\Banner\BannerWasViewedEvent;
use Hifone\Models\Carousel;

class BannerController extends WebController
{
    public function index()
    {
        $carousels = Carousel::visible()->orderBy('order')->get();
        foreach ($carousels as $carousel) {
            $carousel['statistic'] = route('web.banner.show', $carousel->id);
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