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

class AppUserWasActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $activeDate = app('session')->get('app_user_active_date');
            if (!$activeDate || $activeDate != date('Ymd')) {
                $user = Auth::user();
                $user->update([
                    'last_active_time_app' => Carbon::now()->toDateTimeString(),
                    'device' => 3,
                ]);
                app('session')->put('app_user_active_date', date('Ymd'));
            }
        }

        return $next($request);
    }
}
