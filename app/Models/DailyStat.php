<?php

namespace Hifone\Models;

class DailyStat extends BaseModel
{
    protected $table = 'daily_stats';

    protected $fillable = ['date'];

    public $rules = [];

    public function object()
    {
        return $this->morphTo();
    }

    public function scopeSearch($query, $searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'date_start') {
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                $query->where('created_at', '<=', $value);
            }
        }
    }
}