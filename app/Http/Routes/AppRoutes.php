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
        $router->group(['namespace' => 'App\V1', 'prefix' => 'app/v1', 'middleware' => 'api'], function ($router) {
            $router->get('user/info', 'UserController@show');
            $router->post('user/bind', 'UserController@bind');

            $router->get('nodes', 'NodeController@index');
            $router->get('nodes/{node}', 'NodeController@show');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@show');
            $router->get('sections', 'SectionController@index');
            $router->get('threads', 'ThreadController@index');
            $router->get('threads/{thread}', 'ThreadController@show');
            $router->get('threads/{thread}/replies', 'ThreadController@replies');

            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('upload', 'CommonController@upload');
                $router->post('threads', 'ThreadController@store');
            });
        });
    }
}