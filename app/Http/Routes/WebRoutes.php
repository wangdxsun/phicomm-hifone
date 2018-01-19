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
 * 社区Web端（前后端分离）路由
 * This is the api routes class.
 */
class WebRoutes
{
    /**
     * Define the api routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        $router->group([
            'namespace' => 'Web',
            'prefix' => 'web/v1',
            'middleware' => 'web',
            'as' => 'web.'
        ], function ($router) {
            $router->get('emotions', 'GeneralController@emotion');
            $router->get('captcha', 'CommonController@captcha')->name('captcha');

            //内容相关
            $router->get('threads/hot', 'ThreadController@index');
            $router->get('threads/recent', 'ThreadController@recent');
            $router->get('thread/search/{keyword}', 'ThreadController@search')->middleware('web.active');
            $router->get('user/search/{keyword}', 'UserController@search')->middleware('web.active');
            $router->get('threads/{thread}', 'ThreadController@show')->middleware('web.active');
            $router->get('threads/{thread}/replies', 'ThreadController@replies');
            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections')->middleware('web.active');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('nodes/{node}', 'NodeController@show')->where('node', '[0-9]+')->middleware('web.active');
            $router->get('nodes/{node}/recommend', 'NodeController@recommendThreadsOfNode')->where('node', '[0-9]+');
            $router->get('subNodes/{subNode}', 'NodeController@showOfSubNode');
            $router->get('nodes/{node}/subNodes','SubNodeController@index');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@show')->name('banner.show')->middleware('web.active');
            $router->get('report/reason', 'ReportController@reason');

            //登录相关
            $router->post('register/pre', 'PhicommController@preRegister');
            $router->post('register', 'PhicommController@register');
            $router->post('login', 'PhicommController@login');
            $router->post('reset/pre', 'PhicommController@preReset');
            $router->post('reset', 'PhicommController@reset');
            $router->post('verify', 'PhicommController@verify');
            $router->post('bind', 'PhicommController@bind');

            $router->post('auth/login', 'AuthController@login');
            $router->post('auth/logout', 'PhicommController@logout');

            //个人中心
            $router->get('user/info', 'UserController@me')->middleware('web.active');
            $router->get('u/{username}', 'UserController@showByUsername');
            $router->get('users/{user}', 'UserController@show')->where('user', '[0-9]+')->middleware('web.active');
            $router->get('users/{user}/follows', 'UserController@follows');
            $router->get('users/{user}/followers', 'UserController@followers');
            $router->get('users/{user}/threads', 'UserController@threads');
            $router->get('users/{user}/replies', 'UserController@replies');
            $router->get('users/{user}/favorites', 'UserController@favorites');
            $router->get('rank', 'RankController@ranks');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('upload', 'CommonController@upload');
                $router->post('upload/base64', 'CommonController@uploadBase64');
                $router->post('threads', 'ThreadController@store')->middleware('web.active');
                $router->post('replies', 'ReplyController@store');
                $router->post('follow/users/{user}', 'FollowController@user');
                $router->post('follow/threads/{thread}', 'FollowController@thread');
                $router->post('like/threads/{thread}', 'LikeController@thread');
                $router->post('like/replies/{reply}', 'LikeController@reply');
                $router->post('favorite/threads/{thread}', 'FavoriteController@threadFavorite');
                $router->post('report/threads/{thread}', 'ReportController@thread');
                $router->post('report/replies/{reply}', 'ReportController@reply');
                $router->get('notification', 'NotificationController@index');
                $router->get('user/watch', 'NotificationController@watch');
                $router->get('user/credit', 'UserController@credit');
                $router->post('user/avatar', 'UserController@upload');
                $router->post('rank', 'RankController@rankStatus');
                $router->get('rank/count', 'RankController@count');

                $router->get('chats', 'ChatController@chats');
                $router->get('chat/{user}', 'ChatController@messages');
                $router->post('chat/{user}', 'ChatController@store')->where('user', '[0-9]+');

                $router->get('notification', 'NotificationController@index');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->get('notification/watch', 'NotificationController@watch');

                $router->post('logout', 'PhicommController@logout');
            });

            //后台管理员
            $router->group(['middleware' => ['auth', 'role:Admin|Founder|NodeMaster']], function ($router) {
                $router->post('threads/{thread}/excellent', 'ThreadController@excellent');
                $router->post('threads/{thread}/pin', 'ThreadController@pin');
                $router->post('threads/{thread}/sink', 'ThreadController@sink');
            });
        });
    }
}
