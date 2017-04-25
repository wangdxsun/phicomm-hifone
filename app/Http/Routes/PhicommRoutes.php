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
            $router->get('login', 'PhicommController@getLogin')->name('login');
            $router->post('login', 'PhicommController@postLogin');
            $router->get('register', 'PhicommController@getRegister')->name('register');
            $router->post('register', 'PhicommController@postRegister');
            $router->post('verifyCode', 'PhicommController@sendVerifyCode');
            $router->get('create', 'PhicommController@getCreate');
            $router->post('bind', 'PhicommController@postBind');
            $router->get('forget', 'PhicommController@forget');
            $router->post('reset', 'PhicommController@reset');
        });
    }
}