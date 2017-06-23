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

}