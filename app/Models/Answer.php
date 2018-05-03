<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Models;

use Hifone\Models\Scopes\CommonTrait;

class Answer extends BaseModel
{
    use CommonTrait;

    protected $fillable = [];

    protected $hidden = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}