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
class ForumRoutes
{
    /**
     * Define the status page routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     *
     * @return void
     */
    public function map(Registrar $router)
    {
        $router->group(['middleware' => ['web', 'localize']], function (Registrar $router) {
            $router->get('/', 'ThreadController@index')->name('home');
            $router->get('/search', 'ThreadController@search')->name('search');
            $router->get('/excellent', 'HomeController@excellent')->name('excellent');
            $router->get('/feed', 'HomeController@feed')->name('feed');
            $router->get('/captcha', 'CaptchaController@index')->name('captcha');
            $router->get('/go/{slug}', 'NodeController@showBySlug')->name('go');
            $router->get('/get/first/img/url', 'GetFirstImgUrlController@index')->name('test');

            // 兼容老社区
            $router->get('forum.php', 'ThreadController@index');
            $router->get('member.php', 'ThreadController@index');
            $router->get('home.php', 'ThreadController@index');

            $router->group(['middleware' => 'auth'], function (Registrar $router) {
                $router->get('/notification', 'NotificationController@index')->name('notification.index');
                $router->post('/notification/clean', 'NotificationController@clean')->name('notification.clean');
                $router->get('/credit', 'CreditController@index')->name('credit.index');
                $router->get('/pm', 'PmController@index')->name('pm.index');
                $router->get('/messages', 'MessagesController@index')->name('messages.index');
                $router->get('/messages/create', 'MessagesController@create')->name('messages.create');
                $router->post('/messages', 'MessagesController@store')->name('messages.store');
                $router->get('/messages/{id}', 'MessagesController@show')->name('messages.show');
                $router->put('/messages/{id}', 'MessagesController@update')->name('messages.update');
                $router->post('/thread/{thread}/trash', 'ThreadController@postTrash')->name('thread.trash');
            });

            //Sitemap Stuff
            $router->get('/sitemap/threads', 'SitemapController@showThreads')->name('sitemap.threads');
            $router->get('/sitemap/pages', 'SitemapController@showPages')->name('sitemap.pages');
            $router->get('/sitemap/users', 'SitemapController@showUsers')->name('sitemap.users');
            $router->get('/sitemap/nodes', 'SitemapController@showNodes')->name('sitemap.nodes');
            $router->get('/sitemap', 'SitemapController@show')->name('sitemap.show');

            $router->get('/about', 'PageController@about')->name('about');
            $router->get('/contact', 'PageController@contact')->name('contact');
            $router->get('/faq', 'PageController@faq')->name('faq');
            $router->get('/emoji', 'PageController@emoji')->name('emoji');

            $router->resource('node', 'NodeController');
            $router->resource('thread', 'ThreadController');
            $router->resource('pm', 'PmController');
            $router->resource('reply', 'ReplyController', ['only' => ['store']]);
            $router->resource('tag', 'TagController');
        });
    }
}
