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
            $router->get('user/info', 'UserController@show');
            $router->post('user/bind', 'UserController@bind');

            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('nodes/{node}', 'NodeController@show')->name('node.show');
            $router->get('subNodes/{subNode}', 'NodeController@showOfSubNode');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@bannerViewCount')->name('banner.show');
            $router->get('threads', 'ThreadController@index');
            $router->get('threads/search', 'ThreadController@search');
            $router->get('users/search', 'UserController@search');
            $router->get('threads/{thread}', 'ThreadController@show')->where('thread', '[0-9]+');
            $router->get('threads/{thread}/replies', 'ThreadController@replies')->where('thread', '[0-9]+');


            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('upload/base64', 'CommonController@uploadBase64');
                $router->post('upload', 'CommonController@upload');
                $router->post('threads', 'ThreadController@store');
                $router->post('replies', 'ReplyController@store');
                $router->post('follow/user/{user}', 'FollowController@user');
                $router->post('follow/thread/{thread}', 'FollowController@thread');
                $router->post('like/thread/{thread}', 'LikeController@thread');
                $router->post('like/reply/{reply}', 'LikeController@reply');
                $router->post('report/thread/{thread}', 'ReportController@thread');
                $router->post('report/reply/{reply}', 'ReportController@reply');
                $router->post('favorite/thread/{thread}', 'FavoriteController@createOrDeleteFavorite');
                $router->get('users/{user}/favorites', 'UserController@favorites');
            });
        });
    }
}