<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

/**
 * This is the status page routes class.
 */
class UserRoutes
{
    /**
     * Define the status page routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group(['middleware' => ['web', 'localize']], function (Registrar $router) {
            $router->get('/user/{user}/replies', 'UserController@replies')->name('user.replies');
            $router->get('/user/{user}/threads', 'UserController@threads')->name('user.threads');
            $router->get('/user/{user}/follows', 'UserController@follows')->name('user.follows');
            $router->get('/user/{user}/followers', 'UserController@followers')->name('user.followers');
            $router->get('/user/{user}/favorites', 'UserController@favorites')->name('user.favorites');
            $router->get('/user/{user}/credits', 'UserController@credits')->name('user.credits');
            $router->get('/user/{user}/refresh_cache', 'UserController@refreshCache')->name('user.refresh_cache');
            $router->post('/user/{user}/unbind', 'UserController@unbind')->name('user.unbind_oauth');
            $router->get('/user/{user}/access_tokens', 'UserController@accessTokens')->name('user.access_tokens');
            $router->get('/access_token/{token}/revoke', 'UserController@revokeAccessToken')->name('user.access_tokens.revoke');
            $router->get('user/regenerate_login_token', 'UserController@regenerateLoginToken')->name('user.regenerate_login_token');
            //上传avatar
            $router->post('/settings/update-avatar', 'UserController@avatarupdate')->name('user.avatarupdate');
            //上传修改秘密
            $router->post('/settings/resetPassword', 'UserController@resetPassword')->name('user.resetPassword');
            $router->get('/u/{username}', 'UserController@showByUsername')->name('user.home');
            $router->get('/user/city/{name}', 'UserController@city')->name('user.city');
            $router->resource('user', 'UserController');
        });
    }
}
