<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/26
 * Time: 9:59
 */

namespace Hifone\Models;

use AltThree\Validator\ValidatingTrait;
use Carbon\Carbon;
use Hifone\Models\Scopes\ForUser;
use Hifone\Models\Scopes\Recent;
use Hifone\Models\Traits\SearchTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use SearchTrait, ForUser, ValidatingTrait, Recent;

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function getCreatedTimeAttribute()
    {
        return $this->attributes['created_at'];
    }

    public function getUpdatedTimeAttribute()
    {
        return $this->attributes['updated_at'];
    }

    public function logs()
    {
        return $this->morphMany(Log::class, 'logable');
    }
}