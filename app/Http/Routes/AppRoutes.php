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
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('user/bind', 'UserController@bind');
                $router->get('user/info', 'UserController@show');
            });
        });
    }
}