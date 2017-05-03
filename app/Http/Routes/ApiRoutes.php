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
 * This is the api routes class.
 */
class ApiRoutes
{
    /**
     * Define the api routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        $router->group(['namespace' => 'Api', 'prefix' => 'api/v1', 'middleware' => 'api'], function ($router) {

            $router->get('ping', 'GeneralController@ping');
            $router->get('thread', 'ThreadController@index');
            $router->get('thread/{thread}', 'ThreadController@show');
            $router->get('node', 'NodeController@index');
            $router->get('banner', 'BannerController@index');

            //phicomm
            $router->post('register', 'PhicommController@register');
            $router->post('login', 'PhicommController@login');
            $router->post('reset', 'PhicommController@reset');
            $router->get('verify', 'PhicommController@verify');
            $router->post('bind', 'PhicommController@bind');
            $router->get('test', 'PhicommController@test');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->get('follow/user/{user}', 'FollowController@user');
                $router->post('thread', 'ThreadController@store')->middleware('permission:new_thread');
            });
        });
    }
}
