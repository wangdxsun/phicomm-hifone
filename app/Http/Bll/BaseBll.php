<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 9:05
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Events\User\UserWasActiveEvent;
use Hifone\Exceptions\Consts\CommentEx;
use Hifone\Exceptions\Consts\CommonEx;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\BaseModel;
use Auth;
use Hifone\Models\Thread;

class BaseBll
{
    public function isContainsImageOrUrl($str)
    {
        if (substr_count($str,'<a') > 0 && (substr_count($str,'<a') != substr_count($str, '@'))) {
            return true;
        } elseif (substr_count($str,'class="face"') != substr_count($str,'<img')) {
            return true;
        } else {
            return false;
        }
    }

    public function hasImage($body)
    {
        //不全是表情
        return substr_count($body,'class="face"') <> substr_count($body,'<img');
    }

    public function hasUrl($body)
    {
        //有链接，且不全是@
        return substr_count($body,'<a') > 0 && (substr_count($body,'<a') <> substr_count($body, '@'));
    }

    public function hasVideo($body)
    {
        return mb_strpos($body, '<iframe') <> false;
    }

    public function updateOpLog(BaseModel $model, $operation, $reason = null)
    {
        $operator = $operation == '自动审核通过' ? 0 : Auth::id();
        $model->last_op_user_id = $operator;
        $model->last_op_time = Carbon::now()->toDateTimeString();
        $reason && $model->last_op_reason = $reason;
        $model->save();
        $logData['user_id'] = $operator;
        $logData['operation'] = $operation;
        $logData['reason'] = $reason;
        $model->logs()->create($logData);
    }

    public function checkPermission()
    {
        if (Auth::user()->hasRole('NoComment') || Auth::user()->score < 0) {
            throw new HifoneException('你已被禁言', CommonEx::NO_COMMENT);
        }
    }
}