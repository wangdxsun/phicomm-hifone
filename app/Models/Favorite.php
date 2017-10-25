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
}
