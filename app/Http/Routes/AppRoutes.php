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
        $router->group(['namespace' => 'App\V1', 'prefix' => 'app/v1', 'middleware' => 'api', 'as' => 'app.'], function ($router) {
            //个人中心
            $router->get('user/info', 'UserController@me')->middleware('active:app');
            $router->post('user/bind', 'UserController@bind');
            $router->get('users/{user}', 'UserController@show')->where('user', '[0-9]+')->middleware('active:app');
            $router->get('users/{user}/follows', 'UserController@follows')->where('user', '[0-9]+');
            $router->get('users/{user}/sections', 'UserController@sections')->where('user', '[0-9]+');
            $router->get('users/{user}/followers', 'UserController@followers')->where('user', '[0-9]+');
            $router->get('users/{user}/threads', 'UserController@threads')->where('user', '[0-9]+');
            $router->get('users/{user}/replies', 'UserController@replies')->where('user', '[0-9]+');
            $router->get('users/{user}/favorites', 'UserController@favorites')->where('user', '[0-9]+');
            $router->get('users/{user}/questions', 'UserController@questions')->where('user', '[0-9]+');
            $router->get('users/{user}/answers', 'UserController@answers')->where('user', '[0-9]+');
            $router->get('ranks', 'RankController@ranks');

            //内容相关
            $router->get('nodes', 'NodeController@index');
            $router->get('sections', 'NodeController@sections')->middleware('active:app');
            $router->get('subNodes', 'NodeController@subNodes');
            $router->get('subNodes/feedback', 'NodeController@subNodesInFeedback');
            $router->get('nodes/feedback', 'NodeController@nodesInFeedback');
            $router->get('nodes/{node}', 'NodeController@show')->name('node.show')->where('node', '[0-9]+')->middleware('active:app');
            $router->get('nodes/{node}/hot', 'NodeController@hot')->where('node', '[0-9]+');
            $router->get('nodes/{node}/excellent', 'NodeController@excellent')->where('node', '[0-9]+');
            $router->get('nodes/{node}/recent', 'NodeController@recent')->where('node', '[0-9]+');
            $router->get('subNodes/{subNode}', 'NodeController@showOfSubNode')->where('subNode', '[0-9]+');
            $router->get('subNodes/{subNode}/hot', 'NodeController@subNodeHot')->where('subNode', '[0-9]+');
            $router->get('subNodes/{subNode}/recent', 'NodeController@subNodeRecent')->where('subNode', '[0-9]+');
            $router->get('banners', 'BannerController@index');
            $router->get('banners/{carousel}', 'BannerController@bannerViewCount')->name('banner.show')->where('carousel', '[0-9]+')->middleware('active:app');
            $router->get('threads', 'ThreadController@index');
            $router->get('threads/recent', 'ThreadController@recent')->middleware('active:app');
            $router->get('threads/excellent', 'ThreadController@excellent')->middleware('active:app');
            $router->get('threads/search/{keyword}/{a?}/{b?}/{c?}', 'ThreadController@search');
            $router->get('users/search/{keyword}/{a?}/{b?}/{c?}', 'UserController@search');
            $router->get('threads/{thread}', 'ThreadController@show')->where('thread', '[0-9]+')->middleware('active:app');
            $router->get('threads/{thread}/replies/{sort?}', 'ThreadController@replies')->where('sort', 'like|desc|asc')->where('thread', '[0-9]+');
            $router->get('replies/{reply}', 'ReplyController@show');

            //问答相关
            $router->get('questions', 'QuestionController@index');
            $router->get('questions/recent', 'QuestionController@recent');
            $router->get('questions/{question}', 'QuestionController@show')->where('question', '[0-9]+');
            $router->get('questions/{question}/answers', 'QuestionController@answers')->where('question', '[0-9]+');
            $router->get('questions/rewards', 'QuestionController@rewards');
            $router->get('questions/tagTypes', 'TagController@tagTypes');
            $router->get('questions/search/{keyword}/{a?}/{b?}/{c?}', 'QuestionController@search');
            $router->get('answers/search/{keyword}/{a?}/{b?}/{c?}', 'AnswerController@search');
            $router->get('answers/{answer}', 'AnswerController@show')->where('answer', '[0-9]+');
            $router->get('answers/{answer}/comments', 'AnswerController@comments')->where('answer', '[0-9]+');


            // Authorization Required
            $router->group(['middleware' => 'auth:hifone'], function ($router) {
                $router->post('upload', 'CommonController@upload');
                $router->post('upload/base64', 'CommonController@uploadBase64');
                $router->post('threads', 'ThreadController@store')->middleware('active:app');
                $router->post('threads/{thread}/vote', 'ThreadController@vote')->where('thread', '[0-9]+');
                $router->post('threads/{thread}/shared', 'ThreadController@addScoreThreadShared')->where('thread', '[0-9]+');
                $router->post('feedbacks/replies', 'ReplyController@feedback');
                $router->post('feedbacks', 'ThreadController@feedback');
                $router->post('replies', 'ReplyController@store');
                $router->post('follow/user/{user}', 'FollowController@user')->where('user', '[0-9]+');
                $router->post('follow/thread/{thread}', 'FollowController@thread')->where('thread', '[0-9]+');
                $router->post('follow/node/{node}', 'FollowController@node')->where('node', '[0-9]+');
                $router->post('follow/questions/{question}', 'FollowController@question')->where('question', '[0-9]+');
                $router->post('like/thread/{thread}', 'LikeController@thread')->where('thread', '[0-9]+');
                $router->post('like/reply/{reply}', 'LikeController@reply')->where('reply', '[0-9]+');
                $router->post('like/answers/{answer}', 'LikeController@answer')->where('answer', '[0-9]+');
                $router->post('like/comments/{comment}', 'LikeController@comment')->where('comment', '[0-9]+');
                $router->post('report/thread/{thread}', 'ReportController@thread')->where('thread', '[0-9]+');
                $router->post('report/reply/{reply}', 'ReportController@reply')->where('reply', '[0-9]+');
                $router->post('report/question/{question}', 'ReportController@question')->where('question', '[0-9]+');
                $router->post('report/answer/{answer}', 'ReportController@answer')->where('answer', '[0-9]+');
                $router->post('report/comment/{comment}', 'ReportController@comment')->where('comment', '[0-9]+');
                $router->post('favorite/thread/{thread}', 'FavoriteController@createOrDeleteFavorite')->where('thread', '[0-9]+');
                $router->get('user/reply/feedbacks', 'UserController@replyFeedbacks');
                $router->get('user/feedbacks', 'UserController@feedbacks');
                $router->get('user/thread/feedbacks', 'UserController@threadFeedbacks');

                $router->get('user/credit', 'UserController@credit');
                $router->post('user/avatar', 'UserController@upload');
                $router->post('rank', 'RankController@rankStatus');
                $router->get('rank/count', 'RankController@count');

                $router->get('chats/{chat?}', 'ChatController@chats')->where('chat', '[0-9]+');
                $router->get('chat/{user}/{scope}/{chat?}', 'ChatController@messages')->where('user', '[0-9]+')
                    ->where('scope', 'after|before')->where('chat', '[0-9]+')->name('chat.message');
                $router->post('chat/{user}', 'ChatController@store')->where('user', '[0-9]+');
                $router->get('notification', 'NotificationController@index');
                $router->get('notification/reply', 'NotificationController@reply');
                $router->get('notification/at', 'NotificationController@at');
                $router->get('notification/system', 'NotificationController@system');
                $router->get('notification/watch', 'NotificationController@watch');
                $router->get('notification/moment', 'NotificationController@moment');

                $router->post('questions', 'QuestionController@store');
                $router->post('answers', 'AnswerController@store');
                $router->post('answers/invite/users/{user}/questions/{question}', 'AnswerController@invite')->where('user', '[0-9]+')->where('question', '[0-9]+');
                $router->post('answers/adopt/answers/{answer}', 'AnswerController@adopt')->where('answer', '[0-9]+');
                $router->post('comments', 'CommentController@store');

                $router->get('users/{user}/follow/questions', 'UserController@followQuestions')->where('user', '[0-9]+');
                $router->get('users/{user}/invite/{question}/follow/users', 'UserController@followUsers')->where('user', '[0-9]+')->where('question', '[0-9]+');
                $router->get('users/{user}/invite/{question}/expert/users', 'UserController@expertUsers')->where('user', '[0-9]+')->where('question', '[0-9]+');
            });
        });
    }
}