<?php

namespace Hifone\Models;
use Carbon\Carbon;
use Jenssegers\Agent\Facades\Agent;

class Carousel extends BaseModel
{
    //BANNER所在设备
    const H5 = 1;
    const WEB = 2;
    const ANDROID = 4;
    const IOS = 8;
    const ALL_VERSION = 1;
    const SOME_VERSION = 2;

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
        $device = '';
        switch($value)
        {
            case (Carousel::H5):
                $device = 'h5';
                break;
            case (Carousel::WEB):
                $device = 'web';
                break;
            case (Carousel::H5 + Carousel::WEB):
                $device = 'h5/web';
                break;
            case (Carousel::ANDROID):
                $device = 'android';
                break;
            case (Carousel::IOS):
                $device = 'ios';
                break;
            case (Carousel::ANDROID + Carousel::IOS):
                $device = 'android/ios';
                break;
        }
        return $device;
    }

    public function getImageAttribute()
    {
        if (Agent::match('PhiWifiNative') && Agent::match('iPhone')) {
            return $this->attributes['image'] = $this->ios_icon;
        } elseif (Agent::match('PhiWifiNative') && Agent::match('Android')) {
            return $this->attributes['image'] = $this->android_icon;
        } elseif (Agent::match('Android') || Agent::match('iPhone')) {
            return $this->attributes['image'] = $this->h5_icon;
        } else {
            return $this->attributes['image'] = $this->web_icon;
        }
    }
}
