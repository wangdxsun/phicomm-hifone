<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/3/20
 * Time: 10:06
 */

namespace Hifone\Models\Traits;

trait SearchTrait
{
    public function scopeSearch($query, $searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'date_start') {
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                $query->where('created_at', '<=', $value);
            } elseif ($key == 'body') {
                $query->where('body', 'LIKE', "%$value%");
            } elseif ($key == 'username') {
                $query->where('username', 'LIKE', "%$value%");
            } elseif ($key == 'orderType') {
                $query->orderBy($value,'desc');
            } elseif ($key == 'tags') {
                foreach ($value as $tagId) {
                    $query->whereHas('tags', function ($query) use ($tagId){
                        $query->where('tag_id', $tagId);
                    });
                }
            }  else {
                $query->where($key, $value);
            }
        }
    }
}
