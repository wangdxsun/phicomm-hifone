<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:02
 */

namespace Hifone\Models;

use Hifone\Models\Scopes\CommonTrait;

class Comment extends BaseModel
{
    use CommonTrait;

    protected $fillable = [];

    protected $hidden = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}