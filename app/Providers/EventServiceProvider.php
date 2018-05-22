<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Advertisement
        \Hifone\Events\Advertisement\AdvertisementWasUpdatedEvent::class => [
            \Hifone\Handlers\Listeners\Advertisement\RemoveAdvertisementCacheHandler::class,
        ],
         // 增加备注
        \Hifone\Events\Append\AppendWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendAppendNotificationHandler::class,
        ],

        //帖子审核通过(加经验值、数据统计)
        \Hifone\Events\Thread\ThreadWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendThreadNotificationHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
        ],

        //回复审核通过(加经验值、数据统计)
        \Hifone\Events\Reply\ReplyWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendReplyNotificationHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        // 增加经验值后通知用户
        \Hifone\Events\Credit\CreditWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],

        // 帖子、提问被收藏,增加经验值、智慧果，发通知
        \Hifone\Events\Favorite\FavoritedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        //帖子被取消收藏,发帖人相关逻辑
        \Hifone\Events\Favorite\FavoritedWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //收藏，收藏人的相关逻辑
        \Hifone\Events\Favorite\FavoriteWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //取消对帖子的收藏，收藏人的相关逻辑
        \Hifone\Events\Favorite\FavoriteWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

         //关注，只需要添加积分
        \Hifone\Events\Follow\FollowWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        \Hifone\Events\Follow\FollowWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //被关注，添加积分并发送通知，增加智慧果
        \Hifone\Events\Follow\FollowedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        \Hifone\Events\Follow\FollowedWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //帖子被加精华，增加经验值和智慧果
        \Hifone\Events\Excellent\ExcellentWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        // Image

        \Hifone\Events\Image\ImageWasUploadedEvent::class => [
            \Hifone\Handlers\Listeners\Photo\AddPhotoRecordHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //主动点赞（点赞帖子、回复等）
        \Hifone\Events\Like\LikeWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //主动取消点赞（点赞帖子、回复等）
        \Hifone\Events\Like\LikeWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //被点赞，增加经验值和智慧果，发通知
        \Hifone\Events\Like\LikedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],

        //帖子、回复被取消赞
        \Hifone\Events\Like\LikedWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //举报加积分
        \Hifone\Events\Report\ReportWasPassedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        // Links
        \Hifone\Events\Link\LinkWasUpdatedEvent::class => [
            \Hifone\Handlers\Listeners\Link\RemoveLinkCacheHandler::class,
        ],

        //帖子被回复（经验值、智慧果）
        \Hifone\Events\Reply\RepliedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
        ],
        //帖子、回复、提问、回答置顶加经验值、智慧果,发通知
        \Hifone\Events\Pin\PinWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        //下沉
        \Hifone\Events\Pin\SinkWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //上传头像(经验值、智慧果)
        \Hifone\Events\Image\AvatarWasUploadedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
        ],


        \Hifone\Events\Thread\ThreadWasMovedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
            \Hifone\Handlers\Listeners\Thread\UpdateThreadNodesHandler::class,
        ],

        \Hifone\Events\Thread\ThreadWasViewedEvent::class => [
            \Hifone\Handlers\Listeners\Thread\UpdateThreadViewCountHandler::class,
        ],

        \Hifone\Events\Question\QuestionWasViewedEvent::class => [
            \Hifone\Handlers\Listeners\Question\UpdateQuestionViewCountHandler::class,
            \Hifone\Handlers\Listeners\Question\UpdateFollowNewAnswerCountHandler::class,
        ],

        \Hifone\Events\Answer\AnswerWasViewedEvent::class => [
            \Hifone\Handlers\Listeners\Answer\UpdateAnswerViewCountHandler::class,
        ],

        \Hifone\Events\Banner\BannerWasViewedEvent::class => [
            \Hifone\Handlers\Listeners\Banner\UpdateBannerViewCountHandler::class,
        ],

        \Hifone\Events\User\UserWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Identity\ChangeUsernameHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        \Hifone\Events\User\UserWasLoggedinEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\User\UserWasLoggedInHandler::class,
        ],

        \Hifone\Events\User\UserWasLoggedinWebEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\User\UserWasLoggedInWebHandler::class,
        ],

        \Hifone\Events\User\UserWasLoggedinAppEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\User\UserWasLoggedInAppHandler::class,
        ],


        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\QQ\QqExtendSocialite@handle',
            'SocialiteProviders\Weibo\WeiboExtendSocialite@handle',
            'SocialiteProviders\GitLab\GitLabExtendSocialite@handle',
            //'SocialiteProviders\Weixin\WeixinExtendSocialite@handle',
        ],

        \Hifone\Events\Chat\NewChatMessageEvent::class => [
            \Hifone\Handlers\Listeners\Chat\NewChatMessageHandler::class,
        ],


        //帖子移入垃圾箱
        \Hifone\Events\Thread\ThreadWasTrashedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //回复移入垃圾箱
        \Hifone\Events\Reply\ReplyWasTrashedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //修改积分时，判断有没有升级
        \Hifone\Events\Credit\LevelUpEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
        ],

        //帖子被提升时，需要增加智慧果
        \Hifone\Events\Thread\ThreadWasUppedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
        ],

        //帖子被分享时，需要增加智慧果
        \Hifone\Events\Thread\ThreadWasSharedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
        ],

        //版块置顶，增加智慧果（兼容全局置顶）
        \Hifone\Events\Pin\NodePinWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Score\AddScoreHandler::class,
        ],

        //问题（从回收站、待审核变成审核通过）增加经验值，发通知，计数
        \Hifone\Events\Question\QuestionWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendQuestionNotificationHandler::class,
        ],

        //审核通过的提问被删除，扣除经验值
        \Hifone\Events\Question\QuestionWasDeletedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //回答（从回收站、待审核变成审核通过）增加经验值，发通知，计数
        \Hifone\Events\Answer\AnswerWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Question\UpdateFollowNewAnswerCountHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendAnswerNotificationHandler::class,
        ],

        //comment审核通过，发通知
        \Hifone\Events\Comment\CommentWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendCommentNotificationHandler::class,
        ],

        //提问被回答
        \Hifone\Events\Answer\AnsweredWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //回答被回复
        \Hifone\Events\Comment\CommentedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //审核通过的回答被删除，扣除经验值
        \Hifone\Events\Answer\AnswerWasDeletedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Question\UpdateFollowNewAnswerCountHandler::class,
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
