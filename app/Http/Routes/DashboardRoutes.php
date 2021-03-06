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
        //为版主和实习版主开放后台帖子管理和回帖管理的相关权限
        $router->group([
            'middleware' => ['web', 'auth', 'role:Admin|Founder|NodeMaster|NodePraMaster'],
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard',
            'as' => 'dashboard.'
        ], function (Registrar $router) {

            $router->get('/', 'DashboardController@index')->name('index');

            $router->get('thread/audit', 'ThreadController@audit')->name('thread.audit');
            $router->get('thread/trash', 'ThreadController@trashView')->name('thread.trash');
            $router->post('thread/{thread}/audit', 'ThreadController@postAudit');
            $router->post('thread/batchMove', 'ThreadController@batchMoveThread')->name('thread.move');
            $router->post('thread/batchAudit', 'ThreadController@postBatchAudit');//batch audit thread
            $router->post('thread/{thread}/index/to/trash', 'ThreadController@indexToTrash');
            $router->post('thread/{thread}/audit/to/trash', 'ThreadController@auditToTrash');
            $router->post('thread/{thread}/pin', 'ThreadController@pin');
            $router->post('thread/{thread}/node/pin', 'ThreadController@nodePin');
            $router->post('thread/{thread}/sink', 'ThreadController@sink');
            $router->post('thread/{thread}/excellent', 'ThreadController@setExcellent');
            $router->post('thread/{thread}/recycle', 'ThreadController@recycle');
            $router->get('thread/{thread}/heat_offset','ThreadController@getHeatOffset');
            $router->post('thread/{thread}/heat_offset','ThreadController@setHeatOffset');

            $router->post('reply/{reply}/audit', 'ReplyController@postAudit');
            $router->post('reply/batchAudit', 'ReplyController@postBatchAudit');//batch audit reply
            $router->post('reply/{reply}/audit/to/trash', 'ReplyController@auditToTrash');
            $router->post('reply/{reply}/index/to/trash', 'ReplyController@indexToTrash');
            $router->post('reply/{reply}/pin', 'ReplyController@pin');
            $router->post('reply/{reply}/recycle', 'ReplyController@recycle');
            $router->get('reply/audit', 'ReplyController@audit')->name('reply.audit');
            $router->get('reply/trash', 'ReplyController@trashView')->name('reply.trash');

            //问题相关
            $router->get('questions/index', 'QuestionController@index')->name('questions.index');
            $router->get('questions/audit', 'QuestionController@audit')->name('questions.audit');
            $router->get('questions/trash', 'QuestionController@trashView')->name('questions.trash');
            $router->post('questions/{question}/pin', 'QuestionController@pin');
            $router->post('questions/{question}/sink', 'QuestionController@sink');
            $router->post('questions/{question}/excellent', 'QuestionController@setExcellent');
            $router->post('questions/batchMove', 'QuestionController@batchMoveQuestion')->name('question.move');
            $router->post('questions/batchAudit', 'QuestionController@postBatchAudit');//batch audit thread
            $router->post('questions/{question}/audit', 'QuestionController@postAudit');
            $router->post('questions/{question}/recycle', 'QuestionController@recycle');
            $router->get('questions/{question}/edit', 'QuestionController@edit')->name('questions.edit');
            $router->post('questions/{question}/index/to/trash', 'QuestionController@indexToTrash');
            $router->post('questions/{question}/audit/to/trash', 'QuestionController@auditToTrash');

            //回答相关
            $router->get('answers/index', 'AnswerController@index')->name('answers.index');
            $router->get('answers/audit', 'AnswerController@audit')->name('answers.audit');
            $router->get('answers/trash', 'AnswerController@trashView')->name('answers.trash');
            $router->post('answers/{answer}/pin', 'AnswerController@pin');
            $router->post('answers/batchMove', 'AnswerController@batchMoveAnswer');
            $router->post('answers/batchAudit', 'AnswerController@postBatchAudit');//batch audit thread
            $router->post('answers/{answer}/audit', 'AnswerController@postAudit');
            $router->post('answers/{answer}/recycle', 'AnswerController@recycle');
            $router->post('answers/{answer}/index/to/trash', 'AnswerController@indexToTrash');
            $router->post('answers/{answer}/audit/to/trash', 'AnswerController@auditToTrash');

            $router->get('comments/index', 'CommentController@index')->name('comments.index');
            $router->get('comments/audit', 'CommentController@audit')->name('comments.audit');
            $router->get('comments/trash', 'CommentController@trashView')->name('comments.trash');
            $router->post('comments/{comment}/pin', 'CommentController@pin');
            $router->post('comments/batchAudit', 'CommentController@postBatchAudit');//batch audit thread
            $router->post('comments/{comment}/audit', 'CommentController@postAudit');
            $router->post('comments/{comment}/recycle', 'CommentController@recycle');
            $router->post('comments/{comment}/index/to/trash', 'CommentController@indexToTrash');
            $router->post('comments/{comment}/audit/to/trash', 'CommentController@auditToTrash');


        });

        //为版主和实习版主开放后台帖子管理和回帖管理的相关权限--资源路由
        $router->group([
            'middleware' => ['web', 'auth', 'role:Admin|Founder|NodeMaster|NodePraMaster'],
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard'
        ], function (Registrar $router) {
            $router->resource('thread', 'ThreadController');
            $router->resource('question', 'QuestionController');
            $router->resource('reply', 'ReplyController');
            $router->resource('answer', 'AnswerController');
            $router->resource('comment', 'CommentController');
        });

        //限制管理员的特有后台管理权限
        $router->group([
            'middleware' => ['web', 'auth', 'role:Admin|Founder'],
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard',
            'as' => 'dashboard.'
        ], function (Registrar $router) {
            $router->get('test', 'DashboardController@test')->name('test');
            $router->get('markdown', 'DashboardController@markdown')->name('markdown');
            $router->post('user/{user}/avatar', 'UserController@avatar');
            $router->post('user/{user}/comment', 'UserController@comment');
            $router->post('user/{user}/login', 'UserController@login');
            $router->get('reports/thread', 'ReportController@thread')->name('reports.thread');
            $router->get('reports/question', 'ReportController@question')->name('reports.question');
            $router->post('node/{moderator}/audit/to/trash', 'NodeController@auditToTrash');
            $router->post('reports/{report}/trash', 'ReportController@trash');
            $router->post('reports/{report}/ignore', 'ReportController@ignore');

            $router->get('stat', 'StatController@index')->name('stat.index');
            $router->get('stat/node', 'StatController@node')->name('stat.node');
            $router->get('stat/node/{node}', 'StatController@node_detail')->name('stat.node.show');
            $router->get('stat/banner', 'StatController@banner')->name('stat.banner');
            $router->get('stat/user', 'StatController@userCount')->name('stat.user');
            $router->get('stat/user/app', 'StatController@userCountApp')->name('stat.user.app');
            $router->get('stat/user/web', 'StatController@userCountWeb')->name('stat.user.web');
            $router->get('stat/user/h5', 'StatController@userCountH5')->name('stat.user.h5');
            $router->get('stat/threads/count', 'StatController@dailyThreadCount')->name('stat.daily.threads.count');
            $router->get('stat/replies/count', 'StatController@dailyReplyCount')->name('stat.daily.replies.count');
            $router->get('stat/questions/and/answers/count', 'StatController@dailyQuestionsAndAnswersCount')->name('stat.questions.answers.count');
            $router->get('stat/zeroReply', 'StatController@zeroReplyCount')->name('stat.zeroReply');
            $router->get('stat/banner/{carousel}', 'StatController@banner_detail')->name('stat.banner.show');
            $router->get('stat/interaction', 'StatController@userInteraction')->name('stat.interaction');
            $router->get('stat/search', 'StatController@userSearch')->name('stat.search');
            $router->get('stat/search/{date}', 'StatController@userSearchDate')->name('stat.search.date');
            $router->get('wordsExcel/export','WordsExcelController@export')->name('wordsExcel.export');
            $router->post('wordsExcel/import','WordsExcelController@import');
            $router->post('wordsExcel/check','WordsExcelController@check');
            $router->post('word/batchDestroy', 'WordController@batchDestroy');

            $router->get('chat/send','ChatController@sendChat')->name('chat.send');
            $router->get('chat/lists','ChatController@chatLists')->name('chat.lists');
            $router->post('chat/store','ChatController@chatStore')->name('chat.store');

            $router->get('carousel/web/show', 'CarouselController@webShow')->name('carousel.web.show');
            $router->get('carousel/app/hide', 'CarouselController@appHideBanners')->name('carousel.app.hide');
            $router->get('carousel/web/hide', 'CarouselController@webHideBanners')->name('carousel.web.hide');
            $router->get('carousel/app/create', 'CarouselController@createApp')->name('carousel.create.app');
            $router->get('carousel/{carousel}/app/edit', 'CarouselController@editApp')->name('carousel.edit.app');
            $router->patch('carousel/{carousel}/app/update', 'CarouselController@updateApp')->name('carousel.update.app');
            $router->post('carousel/app/store', 'CarouselController@storeApp')->name('carousel.store.app');
            $router->post('carousel/{carousel}/close', 'CarouselController@close')->name('carousel.close');

            //用户标签
            $router->get('tag/{system}','TagController@index')->name('tag');
            $router->get('tag/create/{system}','TagController@create')->name('tag.create');
            $router->get('tag/{tag}/edit/{system}','TagController@edit')->name('tag.edit');
            $router->patch('tag/{tag}/update/{system}','TagController@update')->name('tag.update');
            $router->post('tag/store/{system}','TagController@store')->name('tag.store');
            $router->get('tag/{tag}/destroy/{system}','TagController@destroy')->name('tag.destroy');
            $router->put('user/{user}/tag/update','UserController@tagUpdate')->name('user.tag.update');

            //问题标签
            $router->get('question/tag/{system}','TagController@index')->name('question.tag');
            $router->get('question/tag/create/{system}','TagController@create')->name('question.tag.create');
            $router->get('question/tag/{tag}/edit/{system}','TagController@edit')->name('question.tag.edit');
            $router->post('question/tag/store/{system}','TagController@store')->name('question.tag.store');
            $router->patch('question/tag/{tag}/update/{system}','TagController@update')->name('question.tag.update');
            $router->delete('question/tag/{tag}/destroy/{system}','TagController@destroy')->name('question.tag.destroy');

            //用户标签分类
            $router->get('tag/type/{system}','TagTypeController@tagType')->name('tag.type');
            $router->get('tag/type/create/{system}','TagTypeController@create')->name('tag.type.create');
            $router->get('tag/type/{tagType}/edit/{system}','TagTypeController@edit')->name('tag.type.edit');
            $router->patch('tag/type/{tagType}/update/{system}','TagTypeController@update')->name('tag.type.update');
            $router->post('tag/type/store/{system}','TagTypeController@store')->name('tag.type.store');
            $router->delete('tag/type/{tagType}/{system}','TagTypeController@destroy')->name('tag.type.destroy');

            //问题分类
            $router->get('question/tag/type/{system}','TagTypeController@tagType')->name('question.tag.type');
            $router->get('question/tag/type/create/{system}','TagTypeController@create')->name('question.tag.type.create');
            $router->get('question/tag/type/{tagType}/edit/{system}','TagTypeController@edit')->name('question.tag.type.edit');
            $router->patch('question/tag/type/{tagType}/update/{system}','TagTypeController@update')->name('question.tag.type.update');
            $router->post('question/tag/type/store/{system}','TagTypeController@store')->name('question.tag.type.store');
            $router->delete('question/tag/type/{tagType}/{system}','TagTypeController@destroy')->name('question.tag.type.destroy');



            // Settings
            $router->group(['as' => 'settings.', 'prefix' => 'settings'], function (Registrar $router) {
                $router->get('general', 'SettingsController@showGeneralView')->name('general');
                $router->get('localization', 'SettingsController@showLocalizationView')->name('localization');
                $router->get('customization', 'SettingsController@showCustomizationView')->name('customization');
                $router->get('aboutus', 'SettingsController@showAboutusView')->name('aboutus');
                $router->post('/', 'SettingsController@postSettings');
                $router->post('close', 'SettingsController@autoAuditClose')->name('close');

            });

            // Dashboard API
            $router->group(['prefix' => 'api'], function (Registrar $router) {
                $router->post('link/order', 'ApiController@postUpdateLinkOrder');
                $router->post('section/order', 'ApiController@postUpdateSectionOrder');
                $router->post('node/order', 'ApiController@postUpdateNodeOrder');
                $router->post('subNode/order', 'ApiController@postUpdateSubNodeOrder');
                $router->post('adspace/order', 'ApiController@postUpdateAdspaceOrder');
                $router->post('location/order', 'ApiController@postUpdateLocationOrder');
                $router->post('carousel/order', 'ApiController@postUpdateCarouselOrder');
                $router->post('tag/order', 'ApiController@postUpdateTagOrder');
                $router->post('tagType/order', 'ApiController@postUpdateTagTypeOrder');
            });
        });

        //限制管理员的特有后台管理权限
        $router->group([
            'middleware' => ['web', 'auth', 'role:Admin|Founder'],
            'prefix' => 'dashboard',
            'namespace' => 'Dashboard'
        ], function (Registrar $router) {

            $router->resource('adblock', 'AdblockController');
            $router->resource('adspace', 'AdspaceController');
            $router->resource('advertisement', 'AdvertisementController');
            $router->resource('photo', 'PhotoController');
            $router->resource('page', 'PageController');
            $router->resource('section', 'SectionController');
            $router->resource('node', 'NodeController');
            $router->resource('subNode', 'SubNodeController');

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
//            $router->resource('report', 'ReportController');
            $router->resource('log', 'LogController');
        });

    }


}
