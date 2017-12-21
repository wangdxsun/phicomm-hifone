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
class WebRoutes
{
    /**
     * Define the api routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        $router->group(['namespace' => 'Web', 'prefix' => 'web/v1', 'middleware' => 'api', 'as' => 'web.'], function ($router) {

            $router->get('emotions', 'GeneralController@emotion');

            //内容相关
            $router->get('threads', 'ThreadController@index');
            $router->get('threads/recent', 'ThreadController@recent');
            $router->get('thread/search/{keyword}', 'ThreadController@search');
            $router->get('user/search/{keyword}', 'UserController@search');
            $router->get('threads/{thread}', 'ThreadController@show');
            $router->get('threads/{thread}/replies', 'ThreadController@replies');
            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('nodes/{node}', 'NodeController@show')->where('node', '[0-9]+');;
            $router->get('nodes/{node}/recommend', 'NodeController@recommendThreadsOfNode')->where('node', '[0-9]+');;
            $router->get('subNodes/{subNode}', 'NodeController@showOfSubNode');
            $router->get('nodes/{node}/subNodes','SubNodeController@index');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@show')->name('banner.show');
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
            $router->get('users/{user}/follows', 'UserController@follows');
            $router->get('users/{user}/followers', 'UserController@followers');
            $router->get('users/{user}/threads', 'UserController@threads');
            $router->get('users/{user}/replies', 'UserController@replies');
            $router->get('users/{user}/favorites', 'UserController@favorites');
            $router->get('rank', 'RankController@ranks');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('threads', 'ThreadController@store');
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
                $router->post('chat/{user}', 'ChatController@store');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->post('logout', 'PhicommController@logout');
            });
        });
    }
}
