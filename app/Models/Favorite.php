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

class Favorite extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'thread_id',
        'user_id'
    ];

    /**
     * Favorites can belong to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function isUserFavoritedThread(User $user, $thread)
    {
        return self::forUser($user->id)->where('thread_id', $thread->id)->first();
    }

    //收藏帖子只显示正常和已删除
    public function thread()
    {
        return $this->belongsTo(Thread::class)->whereIn('status', [Thread::VISIBLE, Thread::DELETED]);
    }

    public function scopeOfThread($query, $thread_id)
    {
        return $query->where('thread_id', $thread_id);
    }
}
