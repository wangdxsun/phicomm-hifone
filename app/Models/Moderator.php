<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2017/8/18
 * Time: 14:12
 * 版主
 */
namespace Hifone\Models;

class Moderator extends BaseModel
{
    public $table = 'moderators';

    protected $fillable = [
        'node_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function node()
    {
        return $this->belongsTo(Node::class, 'node_id','id');
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }

}