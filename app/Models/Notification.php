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

use Hifone\Presenters\NotificationPresenter;
use McCool\LaravelAutoPresenter\HasPresenter;

class Notification extends BaseModel implements HasPresenter
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = ['author_id', 'user_id', 'object_id', 'object_type', 'type', 'body'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'author_id' => 'required|int',
        'user_id'   => 'required|int',
        'object_id' => 'required|int',
    ];

    public function object()
    {
        return $this->morphTo();
    }

    /**
     * Notications can belong to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeForAuthor($query, $author_id)
    {
        return $query->where('author_id', $author_id);
    }

    public function scopeForObject($query, $object_id)
    {
        return $query->where('object_id', $object_id);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    //thread_follow 关注帖子尚未考虑
    public function scopeAt($query)
    {
        return $query->whereIn('type', ['reply_mention', 'reply_reply']);
    }

    public function scopeWatch($query)
    {
        return $query->where('type', 'followed_user_new_thread');
    }

    public function scopeReply($query)
    {
        return $query->where('type', 'thread_new_reply');
    }

    public function scopeSystem($query)
    {
        //thread_follow 关注帖子
        //reply_like 点赞回复
        //thread_like 点赞帖子
        //user_follow 用户关注
        //thread_pin 帖子被置顶
        //thread_favorite 收藏帖子
        //thread_mark_excellent 帖子被置为精华
        return $query->whereIn('type', ['reply_like', 'thread_like', 'user_follow']);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'object_id');
    }

    public function reply()
    {
        return $this->belongsTo(Reply::class, 'object_id');
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return NotificationPresenter::class;
    }

}
