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
class AjaxRoutes
{
    /**
     * Define the ajax routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group(['middleware' => ['web', 'auth']], function (Registrar $router) {

            $router->group(['middleware' => 'permission:manage_threads'], function (Registrar $router) {
                $router->post('thread/{thread}/sink', 'ThreadController@sink')->name('thread.sink');
                $router->post('thread/{thread}/excellent', 'ThreadController@excellent')->name('thread.excellent');
                $router->post('thread/{thread}/pin', 'ThreadController@pin')->name('thread.pin');
                $router->delete('thread/{thread}/delete', 'ThreadController@destroy')->name('thread.destroy');
            });

            $router->resource('like', 'LikeController');
            $router->post('/thread/{thread}/append', 'ThreadController@append')->name('thread.append');
            $router->post('/follow/{thread}', 'FollowController@createOrDelete')->name('follow.createOrDelete');
            $router->post('/follow/user/{user}', 'FollowController@createOrDeleteUser')->name('follow.user');
            $router->delete('reply/{reply}/delete', 'ReplyController@destroy')->name('reply.destroy');
            $router->post('/favorite/{thread}', 'FavoriteController@createOrDelete')->name('favorite.createOrDelete');

            //获取通知数
            $router->get('/notification/count', 'NotificationController@count')->name('notification.count');
            $router->any('upload_image', 'UploadController@uploadImage')->name('upload_image');
            $router->post('user/{user}/blocking', 'UserController@blocking')->name('user.blocking')->middleware(['permission:manage_users']);
        });
    }
}
