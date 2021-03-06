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

class Like extends BaseModel
{
    /**
     * Like.
     *
     * @var int
     */
    const LIKE = 1;

    /**
     * Unlike.
     *
     * @var int
     */
    const UNLIKE = -1;

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = ['user_id', 'likeable_id', 'likeable_type', 'rating'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'user_id'        => 'required|int',
        'likeable_id'    => 'required|int',
        'likeable_type'  => 'required|string',
        'rating'         => 'required|int',
    ];

    /**
     * Get all of the owning likeable models.
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * Scope likes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithUp($query)
    {
        return $query->where('rating', self::LIKE);
    }

    /**
     * Scope unlikes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDown($query)
    {
        return $query->where('rating', self::UNLIKE);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('likeable_type', $type);
    }

    public function scopeOfId($query, $id)
    {
        return $query->where('likeable_id', $id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
