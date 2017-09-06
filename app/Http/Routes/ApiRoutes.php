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
        $router->group(['namespace' => 'Api', 'prefix' => 'api/v1', 'middleware' => 'api', 'as' => 'api.'], function ($router) {

            $router->get('/', 'HomeController@index');
            $router->get('ping', 'GeneralController@ping');
            $router->get('exception', 'GeneralController@exception');
            $router->get('emotion', 'GeneralController@emotion');

            //内容相关
            $router->get('thread', 'ThreadController@index');
            $router->get('search', 'CommonController@search');
            $router->get('thread/{thread}', 'ThreadController@show');
            $router->get('thread/{thread}/replies', 'ThreadController@replies');
            $router->get('node', 'NodeController@index');
            $router->get('sections', 'NodeController@sections');
            $router->get('subNodes/{node}','SubNodeController@index');
            $router->get('banner', 'BannerController@index');
            $router->get('banner/{carousel}', 'BannerController@show')->name('banner.show');
            $router->get('node/{node}', 'NodeController@show');
            $router->get('report/reason', 'ReportController@reason');

            //登录相关
            $router->post('register', 'PhicommController@register');
            $router->post('login', 'PhicommController@login');
            $router->post('reset', 'PhicommController@reset');
            $router->post('verify', 'PhicommController@verify');
            $router->post('bind', 'PhicommController@bind');

            //个人中心
            $router->get('user/me', 'UserController@me');
            $router->get('u/{username}', 'UserController@showByUsername');
            $router->get('user/{user}', 'UserController@show')->where('user', '[0-9]+');
            $router->get('user/{user}/follows', 'UserController@follows');
            $router->get('user/{user}/followers', 'UserController@followers');
            $router->get('user/{user}/threads', 'UserController@threads');
            $router->get('user/{user}/replies', 'UserController@replies');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('thread', 'ThreadController@store');
                $router->post('reply', 'ReplyController@store');
                $router->post('follow/user/{user}', 'FollowController@user');
                $router->post('follow/thread/{thread}', 'FollowController@thread');
                $router->post('like/thread/{thread}', 'LikeController@thread');
                $router->post('like/reply/{reply}', 'LikeController@reply');
                $router->post('report/thread/{thread}', 'ReportController@thread');
                $router->post('report/reply/{reply}', 'ReportController@reply');
                $router->get('notification', 'NotificationController@index');
                $router->get('watch', 'NotificationController@watch');
                $router->get('credit', 'UserController@credit');
                $router->post('user/avatar', 'UserController@upload');

                $router->get('chats', 'ChatController@chats');
                $router->get('chat/{user}', 'ChatController@messages');
                $router->post('chat/{user}', 'ChatController@store');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->post('logout', 'PhicommController@logout');
            });
        });
    }
}
