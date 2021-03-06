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

    public static $logableType = [
        'Hifone\Models\Thread' => '帖子',
        'Hifone\Models\Reply' => '回帖',
        'Hifone\Models\Node' => '版块',
        'Hifone\Models\User' => '用户',
        'Hifone\Models\Carousel' => 'banner',
        'Hifone\Models\Report' => '举报',
        'Hifone\Models\Word' => '敏感词',
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
                return '回帖';
            case Report::class:
                return '举报';
            case User::class:
                return '用户';
            case Carousel::class:
                return 'banner';
            case Node::class:
                return '版块';
            case Word::class:
                return '敏感词';
            default:
                return $this->logable_type;
        }
    }
}
