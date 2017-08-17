<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2017/8/16
 * Time: 14:03
 */
namespace Hifone\Models;

class SubNode extends BaseModel
{

    protected $attributes = [
        'node_id' => 0,
    ];

    protected $fillable = [
        'node_id',
        'name',
        'order',
        'status',
        'description',
        'thread_count',
        'reply_count',
        'created_at',
        'updated_at',
    ];

    public $rules = [
        'name'  => 'required|string',
        'order'     => 'int',
        'status'    => 'int',
    ];

    public function node(){
        return $this->belongsTo(Node::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

}