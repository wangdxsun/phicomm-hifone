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

            $router->get('/', 'HomeController@index');
            $router->get('ping', 'GeneralController@ping');

            //内容相关
            $router->get('thread', 'ThreadController@index');
            $router->get('thread/{thread}', 'ThreadController@show');
            $router->get('node', 'NodeController@index');
            $router->get('banner', 'BannerController@index');
            $router->get('node/{node}', 'NodeController@show');
            $router->get('report/reason', 'ReportController@reason');

            //登录相关
            $router->post('register', 'PhicommController@register');
            $router->post('login', 'PhicommController@login');
            $router->post('reset', 'PhicommController@reset');
            $router->post('verify', 'PhicommController@verify');
            $router->post('bind', 'PhicommController@bind');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('thread', 'ThreadController@store')->middleware('permission:new_thread');
                $router->post('reply', 'ReplyController@store')->middleware('permission:new_thread');
                $router->post('follow/user/{user}', 'FollowController@user');
                $router->post('follow/thread/{thread}', 'FollowController@thread');
                $router->post('like/thread/{thread}', 'LikeController@thread');
                $router->post('like/reply/{reply}', 'LikeController@reply');
                $router->post('report/thread/{thread}', 'ReportController@thread');
                $router->post('report/reply/{reply}', 'ReportController@reply');
            });
        });
    }
}
