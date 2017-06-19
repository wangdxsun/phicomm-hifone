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

use Hifone\Events\Banner\BannerWasViewedEvent;

class UpdateBannerViewCountHandler
{
    protected $cache_key = 'banner_viewed';

    public function handle(BannerWasViewedEvent $event)
    {
        $banner = $event->carousel;
        $dailyStats = $banner->dailyStats;
        $dates = $dailyStats->pluck('date');
        dd($dates);

        if (!$this->hasViewedBanner($banner)) {
            $banner->increment('view_count', 1);
            $dailyStats->increment('view_count', 1);
            $this->storeViewedBanner($banner);
        }
        $banner->increment('click_count', 1);
        $dailyStats->increment('click_count', 1);
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
