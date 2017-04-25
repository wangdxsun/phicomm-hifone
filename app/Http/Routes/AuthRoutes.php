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
 * This is the auth routes class.
 */
class AuthRoutes
{
    /**
     * Define the auth routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group([
            'as' => 'auth.',
            'middleware' => ['web', 'localize'],
            'prefix' => 'auth',
            'namespace' => 'Auth'
        ], function (Registrar $router) {
            $router->get('login', 'AuthController@getLogin')->name('login')->middleware('guest');
            $router->post('login', 'AuthController@postLogin')->middleware('guest');
            $router->get('logout', 'AuthController@getLogout')->name('logout')->middleware('auth');
            $router->get('register', 'AuthController@getRegister')->name('register')->middleware('guest');
            $router->post('register', 'AuthController@postRegister')->middleware('guest');
            $router->get('user-banned', 'AuthController@userBanned');
            $router->get('landing', 'AuthController@landing')->name('landing')->middleware('guest');
            $router->get('{provider}', 'AuthController@provider');
            $router->get('{provider}/callback', 'AuthController@callback');
            $router->get('password/reset/{token?}', 'PasswordController@showResetForm');
            $router->post('password/reset', 'PasswordController@reset');
            $router->post('password/email', 'PasswordController@sendResetLinkEmail');
        });
    }
}
