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
 * This is the dashboard routes class.
 */
class DashboardRoutes
{
    /**
     * Define the dashboard routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group([
            'middleware' => ['web', 'auth', 'role:Admin|Founder'],
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard',
            'as' => 'dashboard.'], function (Registrar $router) {

            $router->get('/', 'DashboardController@index')->name('index');
            $router->get('markdown', 'DashboardController@markdown')->name('markdown');
            $router->get('role/admin', 'RoleController@admin')->name('role.admin');
            $router->get('role/user', 'RoleController@user')->name('role.user');
            $router->get('thread/audit', 'ThreadController@audit')->name('thread.audit');
            $router->get('thread/trash', 'ThreadController@trash')->name('thread.trash');
            $router->get('reply/audit', 'ReplyController@audit')->name('reply.audit');
            $router->get('reply/trash', 'ReplyController@trash')->name('reply.trash');
            $router->get('report/audit', 'ReportController@audit')->name('report.audit');
            $router->post('thread/{thread}/audit', 'ThreadController@postAudit');
            $router->post('thread/{thread}/trash', 'ThreadController@postTrash');
            $router->post('reply/{reply}/audit', 'ReplyController@postAudit');
            $router->post('reply/{reply}/trash', 'ReplyController@postTrash');
            $router->post('reply/{reply}/pin', 'ReplyController@pin');
            $router->post('thread/{thread}/pin', 'ThreadController@pin');
            $router->post('thread/{thread}/sink', 'ThreadController@sink');
            $router->post('thread/{thread}/excellent', 'ThreadController@excellent');
            $router->post('report/{report}/trash', 'ReportController@trash');
            $router->post('report/{report}/ignore', 'ReportController@ignore');
            $router->get('test', 'DashboardController@test');
            $router->get('wordsExcel/export','WordsExcelController@export')->name('wordsExcel.export');
            $router->post('wordsExcel/import','WordsExcelController@import');

            // Settings
            $router->group(['as' => 'settings.', 'prefix' => 'settings'], function (Registrar $router) {
                $router->get('general', 'SettingsController@showGeneralView')->name('general');
                $router->get('localization', 'SettingsController@showLocalizationView')->name('localization');
                $router->get('customization', 'SettingsController@showCustomizationView')->name('customization');
                $router->get('aboutus', 'SettingsController@showAboutusView')->name('aboutus');
                $router->post('/', 'SettingsController@postSettings');
            });

            // Dashboard API
            $router->group(['prefix' => 'api'], function (Registrar $router) {
                $router->post('link/order', 'ApiController@postUpdateLinkOrder');
                $router->post('section/order', 'ApiController@postUpdateSectionOrder');
                $router->post('node/order', 'ApiController@postUpdateNodeOrder');
                $router->post('adspace/order', 'ApiController@postUpdateAdspaceOrder');
                $router->post('location/order', 'ApiController@postUpdateLocationOrder');
                $router->post('carousel/order', 'ApiController@postUpdateCarouselOrder');
            });
        });

        //Resources
        $router->group([
            'middleware' => ['web', 'auth', 'role:Admin|Founder'],
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard'], function (Registrar $router) {

            $router->resource('adblock', 'AdblockController');
            $router->resource('adspace', 'AdspaceController');
            $router->resource('advertisement', 'AdvertisementController');
            $router->resource('photo', 'PhotoController');
            $router->resource('page', 'PageController');
            $router->resource('section', 'SectionController');
            $router->resource('node', 'NodeController');
            $router->resource('thread', 'ThreadController');
            $router->resource('reply', 'ReplyController');
            $router->resource('tip', 'TipController');
            $router->resource('location', 'LocationController');
            $router->resource('link', 'LinkController');
            $router->resource('user', 'UserController');
            $router->resource('role', 'RoleController');
            $router->group(['prefix' => 'group'], function (Registrar $router) {
                $router->resource('users', 'UserGroupController');
                $router->resource('admin', 'AdminGroupController');
            });
            $router->resource('word', 'WordController');
            $router->resource('creditRule', 'CreditController');
            $router->resource('notice', 'NoticeController');
            $router->resource('carousel', 'CarouselController');
            $router->resource('report', 'ReportController');
        });

    }
}
