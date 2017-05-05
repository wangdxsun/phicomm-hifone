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

use Auth;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Symfony\Component\HttpFoundation\JsonResponse;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()  || $request->isApi()) {
                return new JsonResponse('Unauthorized.', 401);
            } else {
                $method = 'guest';
                if ($request->method() === 'POST') {
                    app('session')->put('url.intended', back_url());
                    $method = 'to';
                }

                return redirect()->$method('auth/login')
                    ->withInfo(trans('hifone.login.auth_prompt'));
            }
        }

        return $next($request);
    }
}
