<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:45
 */

namespace Hifone\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;

class PhicommRoutes
{
    public function map(Registrar $router)
    {
        $router->group(['as' => 'phicomm.','middleware' => ['web', 'localize'], 'prefix' => 'phicomm'], function (Registrar $router) {
            $router->get('login', [
                'as' => 'login',
                'uses' => 'PhicommController@getLogin',
                'middleware' => 'guest',
            ]);
            $router->post('login', [
                'uses' => 'PhicommController@postLogin',
                'middleware' => 'guest',
            ]);
            $router->get('register', [
                'as' => 'register',
                'uses' => 'PhicommController@getRegister',
                'middleware' => 'guest',
            ]);
            $router->post('register', [
                'uses' => 'PhicommController@postRegister',
                'middleware' => 'guest',
            ]);
        });
    }
}