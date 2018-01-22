<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Middleware;

use Closure;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserWasActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $device)
    {
        if ($request->route()->uri() == $device.'/v1/threads' && !$request->has('page')) {
            return $next($request);
        }
        if ($device == 'api') {
            $this->updateActiveTime('user_active_date','last_active_time',1);
        } elseif ($device == 'web') {
            $this->updateActiveTime('web_user_active_date','last_active_time_web',2);
        } else {
            $this->updateActiveTime('app_user_active_date','last_active_time_app',3);
        }
        return $next($request);
    }

    public function updateActiveTime($session_name, $column_name, $device)
    {
        if (Auth::check()) {
            $activeDate = app('session')->get($session_name);
            if (!$activeDate || $activeDate != date('Ymd')) {
                $user = Auth::user();
                $user->update([
                    $column_name => Carbon::now()->toDateTimeString(),
                    'device' => $device,
                ]);
                app('session')->put($session_name, date('Ymd'));
            }
        }
    }
}

