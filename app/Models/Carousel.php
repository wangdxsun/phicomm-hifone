<?php

namespace Hifone\Models;

class Carousel extends BaseModel
{
    protected $fillable = [
        'image',
        'type',
        'url',
        'order',
        'description',
        'user_id',
        'visible',
        'created_at',
        'updated_at',
    ];

    protected $hidden = ['id', 'order', 'description', 'user_id', 'visible', 'last_op_user_id', 'last_op_time', 'view_count', 'click_count', 'created_at', 'updated_at'];

    public $rules = [
        'image'   => 'string|required',
        'order'  => 'int',
        'url' => 'string|required',
        'description' => 'string|required'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function dailyStats()
    {
        return $this->morphMany(DailyStat::class,'object');
    }

    public function scopeVisible($query)
    {
        return $query->where('visible', 1);
    }

    public function getUrlAttribute()
    {
        return route('banner.show', ['carousel' => $this->id]);
    }

    public function getJumpUrlAttribute()
    {
        return $this->attributes['url'];
    }
}
