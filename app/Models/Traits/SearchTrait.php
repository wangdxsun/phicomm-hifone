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
        $res = null;
        foreach ($searches as $key => $value) {
            if ($key == 'date_start') {
                $res = $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                $res = $query->where('created_at', '<=', $value);
            } elseif ($key == 'body') {
                $res = $query->where('body', 'LIKE', "%$value%");
            } else {
                $res = $query->where($key, $value);
            }
        }
        return $res;
    }
}