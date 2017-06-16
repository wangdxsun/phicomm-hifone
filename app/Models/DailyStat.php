<?php
namespace Hifone\Models;

class DailyStat extends BaseModel
{
    protected $table = 'daily_stats';

    public function object()
    {
        return $this->morphTo();
    }
}