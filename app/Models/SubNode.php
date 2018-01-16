<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2017/8/16
 * Time: 14:03
 */
namespace Hifone\Models;
use DB;
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
        'is_feedback',
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
        'name'      => 'required|string',
        'order'     => 'int',
        'status'    => 'int',
        'prompt'    => 'string|min:10|max:40',
        'description'    => 'string|min:0|max:100',
    ];

    public $validationMessages = [
        'name.required' => '子版块名称是必填字段',
        'name.min' => '子版块名称最少2个字符',
        'name.max' => '子版块名称最多50个字符',
        'prompt.min' => '提示语最少10个字符',
        'prompt.max' => '提示语最多40个字符',
        'description.min' => '子版块描述最少0个字符',
        'description.max' => '子版块描述最多100个字符',
    ];

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    //是否在意见反馈显示该子版块:0不显示，1显示
    public function scopeFeedback($query)
    {
        return $query->where('is_feedback', 1);
    }

    public function getReplies($value)
    {
        $replies = DB::select('select * from replies left join (select id from threads where sub_node_id = ?) as stat 
                                    on thread_id = stat.id
                                    where replies.status = 0
                                    or replies.status = -3',[$value]);
        return $replies;
    }

}