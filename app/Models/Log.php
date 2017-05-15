<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Models;

class Log extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'logable_id',
        'logable_type',
        'operation',
        'reason',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'user_id'   => 'required|int',
        'operation' => 'required',
    ];

    public function logable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getObjectTypeAttribute()
    {
        switch ($this->logable_type) {
            case Thread::class:
                return '帖子';
            case Reply::class:
                return '回复';
            case Report::class:
                return '举报';
            case User::class:
                return '用户';
            case Carousel::class:
                return 'banner';
            case Node::class:
                return '板块';
            default:
                return $this->logable_type;
        }
    }
}
