<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/1
 * Time: 15:45
 */

namespace Hifone\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class AppRoutes
{
    public function map(Registrar $router)
    {
        $router->group(['namespace' => 'App\V1', 'prefix' => 'app/v1', 'middleware' => 'api', 'as' => 'app.'], function ($router) {
            //个人中心
            $router->get('user/info', 'UserController@me');
            $router->post('user/bind', 'UserController@bind');
            $router->get('users/{user}', 'UserController@show')->where('user', '[0-9]+');
            $router->get('users/{user}/follows', 'UserController@follows');
            $router->get('users/{user}/followers', 'UserController@followers');
            $router->get('users/{user}/threads', 'UserController@threads');
            $router->get('users/{user}/replies', 'UserController@replies');
            $router->get('users/{user}/favorites', 'UserController@favorites');

            //内容相关
            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('subNodes/feedback', 'NodeController@subNodesInFeedback');
            $router->get('nodes/{node}', 'NodeController@show')->name('node.show');
            $router->get('subNodes/{subNode}', 'NodeController@showOfSubNode');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@bannerViewCount')->name('banner.show');
            $router->get('threads', 'ThreadController@index');
            $router->get('threads/search/{keyword}', 'ThreadController@search');
            $router->get('users/search/{keyword}', 'UserController@search');
            $router->get('threads/{thread}', 'ThreadController@show')->where('thread', '[0-9]+');
            $router->get('threads/{thread}/replies', 'ThreadController@replies')->where('thread', '[0-9]+');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('upload/base64', 'CommonController@uploadBase64');
                $router->post('upload', 'CommonController@upload');
                $router->post('threads', 'ThreadController@store');
                $router->post('feedbacks', 'ThreadController@feedback');
                $router->post('replies', 'ReplyController@store');
                $router->post('follow/user/{user}', 'FollowController@user')->where('user', '[0-9]+');
                $router->post('follow/thread/{thread}', 'FollowController@thread')->where('thread', '[0-9]+');
                $router->post('like/thread/{thread}', 'LikeController@thread')->where('thread', '[0-9]+');
                $router->post('like/reply/{reply}', 'LikeController@reply')->where('reply', '[0-9]+');
                $router->post('report/thread/{thread}', 'ReportController@thread')->where('thread', '[0-9]+');
                $router->post('report/reply/{reply}', 'ReportController@reply')->where('reply', '[0-9]+');
                $router->post('favorite/thread/{thread}', 'FavoriteController@createOrDeleteFavorite')->where('thread', '[0-9]+');
                $router->get('user/feedbacks', 'UserController@feedbacks');
                $router->get('user/credit', 'UserController@credit');
                $router->post('user/avatar', 'UserController@upload');

                $router->get('chats', 'ChatController@chats');
                $router->get('chat/{user}/{scope}/{chat?}', 'ChatController@messages')->where('user', '[0-9]+')
                    ->where('scope', 'after|before')->where('chat', '[0-9]+')->name('chat.message');
                $router->post('chat/{user}', 'ChatController@store');
                $router->get('notification', 'NotificationController@index');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->get('notification/watch', 'NotificationController@watch');
            });
        });
    }
}