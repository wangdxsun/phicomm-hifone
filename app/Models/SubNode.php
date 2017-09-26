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
        'is_prompt',
        'prompt',
    ];

    protected $hidden = [
        'node_id',
        'order',
        'stats',
        'reply_count',
        'last_op_user_id',
        'last_op_time',
        'created_at',
        'updated_at',
    ];

    public $rules = [
        'name'  => 'required|string',
        'order'     => 'int',
        'status'    => 'int',
        'prompt'    => 'string|min:10|max:40',
    ];

    public $validationMessages = [
        'name.required' => '子版块名称是必填字段',
        'name.min' => '子版块名称最少2个字符',
        'name.max' => '子版块名称最多50个字符',
        'prompt.required' => '提示语是必填字段',
        'prompt.min' => '提示语最少10个字符',
        'prompt.max' => '提示语最多40个字符',
        'description.min' => '子版块描述最少0个字符',
        'description.max' => '子版块描述最多100个字符',
    ];

    public function node(){
        return $this->belongsTo(Node::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

}