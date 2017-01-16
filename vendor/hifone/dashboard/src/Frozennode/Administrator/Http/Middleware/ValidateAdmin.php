<?php

namespace Frozennode\Administrator\Http\Middleware;

use Closure;

class ValidateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $configFactory = app('admin_config_factory');

        $config = include __DIR__.'/../../../../config/administrator.php';

        //get the admin check closure that should be supplied in the config
        $permission = $config['permission'];

        //if this is a simple false value, send the user to the login redirect
        if (!$response = $permission()) {
            $loginUrl    = url($config['login_path']);
            $redirectKey = $config['login_redirect_key'];
            $redirectUri = $request->url();

            return redirect()->guest($loginUrl)->with($redirectKey, $redirectUri);
        }

        //otherwise if this is a response, return that
        elseif (is_a($response, 'Illuminate\Http\JsonResponse') || is_a($response, 'Illuminate\Http\Response')) {
            return $response;
        }

        //if it's a redirect, send it back with the redirect uri
        elseif (is_a($response, 'Illuminate\\Http\\RedirectResponse')) {
            $redirectKey = $config['login_redirect_key'];
            $redirectUri = $request->url();

            return $response->with($redirectKey, $redirectUri);
        }

        return $next($request);
    }
}
