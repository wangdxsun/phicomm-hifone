<?php

namespace Hifone\Models;
use Carbon\Carbon;

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
        'start_display',
        'end_display',
        'start_version',
        'end_version',
        'device',
        'web_icon',
        'h5_icon',
        'ios_icon',
        'android_icon',
    ];

    protected $hidden = ['id', 'order', 'description', 'user_id', 'visible', 'last_op_user_id', 'last_op_time', 'view_count', 'click_count', 'created_at', 'updated_at'];

    public $rules = [
        'order'         => 'int',
        'url'           => 'string|required',
        'description'   => 'string|required',
        'start_display' => 'required',
        'end_display'   => 'required',
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
        return $query->where('start_display', '<=', Carbon::now())->where('end_display', '>=', Carbon::now());
    }

    public function scopeHide($query)
    {
        return $query->where('start_display', '>=', Carbon::now())->orWhere('end_display', '<=', Carbon::now());
    }

    public function getDevice($value)
    {
        $system= '';
        switch($value)
        {
            case 1:
                $system = 'h5';
                break;
            case 2:
                $system = 'web';
                break;
            case 3:
                $system = 'h5/web';
                break;
            case 4:
                $system = 'android';
                break;
            case 8:
                $system = 'ios';
                break;
            case 12:
                $system = 'android/ios';
                break;
        }
        return $system;
    }

}
