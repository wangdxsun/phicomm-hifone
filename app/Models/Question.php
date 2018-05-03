<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:45
 */

namespace Hifone\Models;

use Hifone\Models\Scopes\CommonTrait;
use Hifone\Models\Traits\Taggable;

class Question extends BaseModel
{
    use CommonTrait, Taggable;

    protected $fillable = [];

    protected $hidden = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}