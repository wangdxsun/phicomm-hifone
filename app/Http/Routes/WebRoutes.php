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
 * 社区Web端（前后端分离）路由
 * This is the api routes class.
 */
class WebRoutes
{
    /**
     * Define the api routes.
     *
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function map(Registrar $router)
    {
        $router->group(['namespace' => 'Web', 'prefix' => 'web/v1', 'middleware' => 'web', 'as' => 'web.'], function ($router) {
            $router->get('emotions', 'GeneralController@emotion');
            $router->get('captcha', 'CommonController@captcha')->name('captcha');

            //内容相关
            $router->get('threads/hot', 'ThreadController@index');
            $router->get('threads/recent', 'ThreadController@recent')->middleware('active:web');
            $router->get('threads/excellent', 'ThreadController@excellent')->middleware('active:web');
            $router->get('thread/search/{keyword}/{a?}/{b?}/{c?}', 'ThreadController@search')->middleware('active:web');
            $router->get('user/search/{keyword}/{a?}/{b?}/{c?}', 'UserController@search')->middleware('active:web');
            $router->get('threads/{thread}', 'ThreadController@show')->where('thread', '[0-9]+')->middleware('active:web');
            $router->get('threads/{thread}/replies/{sort?}', 'ThreadController@replies')->where('thread', '[0-9]+')->where('sort', 'like|desc|asc');
            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections')->middleware('active:web');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('nodes/{node}', 'NodeController@show')->where('node', '[0-9]+')->middleware('active:web');
            $router->get('nodes/{node}/hot', 'NodeController@hot')->where('node', '[0-9]+');
            $router->get('nodes/{node}/excellent', 'NodeController@excellent')->where('node', '[0-9]+');
            $router->get('nodes/{node}/recent', 'NodeController@recent')->where('node', '[0-9]+');
            $router->get('nodes/{node}/recommend', 'NodeController@recommendThreadsOfNode')->where('node', '[0-9]+');
            $router->get('subNodes/{subNode}/{sort?}', 'NodeController@showOfSubNode')->where('subNode', '[0-9]+')->where('sort', 'hot|recent|excellent');
            $router->get('nodes/{node}/subNodes','SubNodeController@index');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@show')->name('banner.show')->middleware('active:web');
            $router->get('report/reason', 'ReportController@reason');

            //问答相关
            $router->get('questions', 'QuestionController@index');
            $router->get('questions/excellent', 'QuestionController@excellent');
            $router->get('questions/{question}', 'QuestionController@show')->where('question', '[0-9]+');
            $router->get('questions/{question}/answers', 'QuestionController@answers')->where('question', '[0-9]+');
            $router->get('questions/rewards', 'QuestionController@rewards');
            $router->get('questions/tags', 'TagController@tags');
            $router->get('questions/tagTypes', 'TagController@tagTypes');
            $router->get('questions/search/{keyword}/{a?}/{b?}/{c?}', 'QuestionController@search');
            $router->get('answers/search/{keyword}/{a?}/{b?}/{c?}', 'AnswerController@search');
            $router->get('answers/{answer}', 'AnswerController@show')->where('answer', '[0-9]+');
            $router->get('answers/{answer}/comments', 'AnswerController@comments')->where('answer', '[0-9]+');



            //登录相关
            $router->post('register/pre', 'PhicommController@preRegister');
            $router->post('register', 'PhicommController@register');
            $router->post('login', 'PhicommController@login');
            $router->post('reset/pre', 'PhicommController@preReset');
            $router->post('reset', 'PhicommController@reset');
            $router->post('verify', 'PhicommController@verify');
            $router->post('bind', 'PhicommController@bind');

            $router->post('auth/login', 'AuthController@login');
            $router->post('auth/logout', 'PhicommController@logout');

            //个人中心
            $router->get('user/info', 'UserController@me')->middleware('active:web');
            $router->get('u/{username}', 'UserController@showByUsername');
            $router->get('users/{user}', 'UserController@show')->where('user', '[0-9]+')->middleware('active:web');
            $router->get('users/{user}/follows', 'UserController@follows')->where('user', '[0-9]+');
            $router->get('users/{user}/followers', 'UserController@followers')->where('user', '[0-9]+');
            $router->get('users/{user}/threads', 'UserController@threads')->where('user', '[0-9]+');
            $router->get('users/{user}/replies', 'UserController@replies')->where('user', '[0-9]+');
            $router->get('users/{user}/favorites', 'UserController@favorites')->where('user', '[0-9]+');
            $router->get('users/{user}/drafts', 'UserController@drafts')->where('user', '[0-9]+');
            $router->get('users/{user}/questions', 'UserController@questions')->where('user', '[0-9]+');
            $router->get('users/{user}/answers', 'UserController@answers')->where('user', '[0-9]+');
            $router->get('rank', 'RankController@ranks');
            $router->get('ranks', 'RankController@ranks');

            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('upload', 'CommonController@upload');
                $router->post('upload/base64', 'CommonController@uploadBase64');
                $router->post('threads', 'ThreadController@store')->middleware('active:web');
                $router->post('threads/draft/{thread}', 'ThreadController@store')->where('thread', '[0-9]+')->middleware('active:web');
                $router->post('drafts', 'ThreadController@storeDraft');
                $router->post('threads/{thread}', 'ThreadController@update')->where('thread', '[0-9]+');
                $router->post('drafts/{thread}', 'ThreadController@updateDraft')->where('thread', '[0-9]+');
                $router->get('levels', 'ThreadController@voteLevels');
                $router->post('threads/{thread}/vote', 'ThreadController@vote')->where('thread', '[0-9]+');
                $router->delete('threads/{thread}', 'ThreadController@delete')->where('thread', '[0-9]+');
                $router->post('replies', 'ReplyController@store');
                $router->post('follow/users/{user}', 'FollowController@user')->where('user', '[0-9]+');
                $router->post('follow/threads/{thread}', 'FollowController@thread')->where('thread', '[0-9]+');
                $router->post('follow/questions/{question}', 'FollowController@question')->where('question', '[0-9]+');
                $router->post('like/threads/{thread}', 'LikeController@thread')->where('thread', '[0-9]+');
                $router->post('like/replies/{reply}', 'LikeController@reply')->where('reply', '[0-9]+');
                $router->post('like/answers/{answer}', 'LikeController@answer')->where('answer', '[0-9]+');
                $router->post('like/comments/{comment}', 'LikeController@comment')->where('comment', '[0-9]+');
                $router->post('favorite/threads/{thread}', 'FavoriteController@threadFavorite')->where('thread', '[0-9]+');
                $router->post('report/threads/{thread}', 'ReportController@thread')->where('thread', '[0-9]+');
                $router->post('report/replies/{reply}', 'ReportController@reply')->where('reply', '[0-9]+');
                $router->post('report/question/{question}', 'ReportController@question')->where('question', '[0-9]+');
                $router->post('report/answer/{answer}', 'ReportController@answer')->where('answer', '[0-9]+');
                $router->post('report/comment/{comment}', 'ReportController@comment')->where('comment', '[0-9]+');
                $router->get('notification', 'NotificationController@index');
                $router->get('user/watch', 'NotificationController@watch');
                $router->get('user/credit', 'UserController@credit');
                $router->post('user/avatar', 'UserController@upload');
                $router->post('rank', 'RankController@rankStatus');
                $router->get('rank/count', 'RankController@count');

                $router->get('chats', 'ChatController@chats');
                $router->get('chat/{user}', 'ChatController@messages')->where('user', '[0-9]+');
                $router->post('chat/{user}', 'ChatController@store')->where('user', '[0-9]+');

                $router->get('notification', 'NotificationController@index');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->get('notification/watch', 'NotificationController@watch');
                $router->get('notification/qa', 'NotificationController@qa');
                $router->get('notification/moment', 'NotificationController@moment');

                $router->post('logout', 'PhicommController@logout');

                $router->post('questions', 'QuestionController@store');
                $router->post('questions/{question}/pin', 'QuestionController@pin')->where('question', '[0-9]+');
                $router->post('questions/{question}/excellent', 'QuestionController@setExcellent')->where('question', '[0-9]+');
                $router->post('answers', 'AnswerController@store');
                $router->post('answers/{answer}/pin', 'AnswerController@pin')->where('answer', '[0-9]+');
                $router->post('comments', 'CommentController@store');

                $router->get('users/{user}/follow/questions', 'UserController@followQuestions')->where('user', '[0-9]+');
                $router->get('users/{user}/invite/{question}/follow/users', 'UserController@followUsers')->where('user', '[0-9]+')->where('question', '[0-9]+');
                $router->get('users/{user}/invite/{question}/expert/users', 'UserController@expertUsers')->where('user', '[0-9]+')->where('question', '[0-9]+');
                $router->get('users/invite/search/{keyword}/{question}', 'UserController@searchUsers')->where('question', '[0-9]+');

            });

            //后台管理员
            $router->group(['middleware' => ['auth', 'role:Admin|Founder|NodeMaster']], function ($router) {
                $router->post('threads/{thread}/excellent', 'ThreadController@setExcellent')->where('thread', '[0-9]+');
                $router->post('threads/{thread}/pin', 'ThreadController@pin')->where('thread', '[0-9]+');
                $router->post('threads/{thread}/sink', 'ThreadController@sink')->where('thread', '[0-9]+');
                $router->get('threads/{thread}/vote/{option?}', 'ThreadController@viewVoteResult')->where('thread', '[0-9]+');
            });
        });
    }
}
