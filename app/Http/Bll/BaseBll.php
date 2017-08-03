<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 9:05
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BaseBll
{
    public function isContainsImageOrUrl($str)
    {
        if (Str::contains($str,['<a'])) {
            return true;
        } elseif ( substr_count($str,'class="face"') != substr_count($str,'<img')) {
            return true;
        } else {
            return false;
        }
    }

    public function updateOpLog(BaseModel $model, $operation, $reason = null)
    {
        $model->last_op_user_id = Auth::id();
        $model->last_op_time = Carbon::now()->toDateTimeString();
        $reason && $model->last_op_reason = $reason;
        $model->save();
        $logData['user_id'] = Auth::id();
        $logData['operation'] = $operation;
        $logData['reason'] = $reason;
        $model->logs()->create($logData);
    }

}