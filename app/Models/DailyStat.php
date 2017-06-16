<?php
namespace Hifone\Models;

class DailyStat extends BaseModel
{
    protected $table = 'daily_stats';

    public function dailyBanners()
    {
        return $this->morphTo();
    }

    public function dailyNodes()
    {
        return $this->morphTo();
    }
}