<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:42
 */

namespace Hifone\Models\Scopes;

trait CommonTrait
{
    /**
     * Scope a query to only given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query order by created desc.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeExcellent($query)
    {
        return $query->orderByDesc('is_excellent')->orderByDesc('order');
    }
}