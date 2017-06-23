<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Banner;
use Carbon\Carbon;
use Hifone\Events\Banner\BannerWasViewedEvent;

class UpdateBannerViewCountHandler
{
    protected $cache_key = 'banner_viewed';

    public function handle(BannerWasViewedEvent $event)
    {
        $banner = $event->carousel;
        if ($banner->dailyStats()->where('date', Carbon::today()->toDateString())->count() == 0) {
            $data = [
                'date' => Carbon::today()->toDateString(),
            ];
            $dailyStat = $banner->dailyStats()->create($data);
        } else{
            $dailyStat = $banner->dailyStats()->where('date', Carbon::today()->toDateString());
        }

        if (!$this->hasViewedBanner($banner)) {
            $banner->increment('view_count', 1);
            $dailyStat->increment('view_count', 1);
            $this->storeViewedBanner($banner);
        }
        $banner->increment('click_count', 1);
        $dailyStat->increment('click_count', 1);
    }

    protected function hasViewedBanner($banner)
    {
        return array_key_exists($banner->id, $this->getViewedBanners());
    }

    protected function getViewedBanners()
    {
        return app('session')->get($this->cache_key, []);
    }

    protected function storeViewedBanner($banner)
    {
        $key = $this->cache_key.'.'.$banner->id;

        app('session')->put($key, time());
    }

}
