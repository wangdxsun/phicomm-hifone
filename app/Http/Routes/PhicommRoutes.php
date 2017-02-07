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
        $router->group(['as' => 'phicomm.','middleware' => ['web', 'localize', 'guest'], 'prefix' => 'phicomm'], function (Registrar $router) {
            $router->get('login', [
                'as' => 'login',
                'uses' => 'PhicommController@getLogin',
            ]);
            $router->post('login', 'PhicommController@postLogin');
            $router->get('register', [
                'as' => 'register',
                'uses' => 'PhicommController@getRegister',
            ]);
            $router->post('register', 'PhicommController@postRegister');
            $router->post('verifyCode', 'PhicommController@sendVerifyCode');
        });
    }
}