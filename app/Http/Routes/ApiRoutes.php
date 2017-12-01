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
            $router->get('emotions', 'GeneralController@emotion');

            //内容相关
            $router->get('threads', 'ThreadController@index');
            $router->get('thread/search/{keyword}', 'ThreadController@search');
            $router->get('user/search/{keyword}', 'UserController@search');
            $router->get('threads/{thread}', 'ThreadController@show')->where('thread', '[0-9]+');
            $router->get('threads/{thread}/replies', 'ThreadController@replies')->where('thread', '[0-9]+');
            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('nodes/{node}', 'NodeController@show')->where('node', '[0-9]+');
            $router->get('subNodes/{subNode}', 'NodeController@showOfSubNode')->where('subNode', '[0-9]+');
            $router->get('nodes/{node}/subNodes','SubNodeController@index')->where('node', '[0-9]+');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@show')->name('banner.show')->where('carousel', '[0-9]+');
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
            $router->get('users/{user}', 'UserController@show')->where('user', '[0-9]+');
            $router->get('users/{user}/follows', 'UserController@follows')->where('user', '[0-9]+');
            $router->get('users/{user}/followers', 'UserController@followers')->where('user', '[0-9]+');
            $router->get('users/{user}/threads', 'UserController@threads')->where('user', '[0-9]+');
            $router->get('users/{user}/replies', 'UserController@replies')->where('user', '[0-9]+');
            $router->get('users/{user}/favorites', 'UserController@favorites')->where('user', '[0-9]+');
            $router->get('rank', 'RankController@ranks');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('threads', 'ThreadController@store');
                $router->post('replies', 'ReplyController@store');
                $router->post('follow/users/{user}', 'FollowController@user')->where('user', '[0-9]+');
                $router->post('follow/threads/{thread}', 'FollowController@thread')->where('thread', '[0-9]+');
                $router->post('like/threads/{thread}', 'LikeController@thread')->where('thread', '[0-9]+');
                $router->post('like/replies/{reply}', 'LikeController@reply')->where('reply', '[0-9]+');
                $router->post('favorite/threads/{thread}', 'FavoriteController@threadFavorite')->where('thread', '[0-9]+');
                $router->post('report/threads/{thread}', 'ReportController@thread')->where('thread', '[0-9]+');
                $router->post('report/replies/{reply}', 'ReportController@reply')->where('reply', '[0-9]+');
                $router->get('notification', 'NotificationController@index');
                $router->get('user/watch', 'NotificationController@watch');
                $router->get('user/credit', 'UserController@credit');
                $router->post('user/avatar', 'UserController@upload');
                $router->post('rank', 'RankController@rankStatus');
                $router->get('rank/count', 'RankController@count');

                $router->get('chats', 'ChatController@chats');
                $router->get('chat/{user}', 'ChatController@messages')->where('user', '[0-9]+');
                $router->post('chat/{user}', 'ChatController@store')->where('user', '[0-9]+');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->post('logout', 'PhicommController@logout');
            });
        });
    }
}
