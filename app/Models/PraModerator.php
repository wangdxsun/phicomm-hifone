<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2018/2/28
 * Time: 17:05
 */

namespace Hifone\Models;


class PraModerator extends BaseModel
{
    public $table = 'pra_moderators';

    protected $fillable = [
        'node_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    //查询
    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}