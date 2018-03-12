<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/3/12
 * Time: 10:50
 */

namespace Hifone\Models;


class OptionUser extends BaseModel
{
    public $table = 'option_user';

    protected $fillable = [
        'option_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}