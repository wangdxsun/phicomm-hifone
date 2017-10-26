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

        // 增加经验值后通知用户
        \Hifone\Events\Credit\CreditWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],

        // Favorite帖子被收藏
        \Hifone\Events\Favorite\FavoriteWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        //帖子被取消收藏
        \Hifone\Events\Favorite\FavoriteWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //帖子被取消收藏
        \Hifone\Events\Favorite\FavoriteThreadWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //帖子被取消收藏
        \Hifone\Events\Favorite\FavoriteThreadWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

         //关注，只需要添加积分
        \Hifone\Events\Follow\FollowWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        \Hifone\Events\Follow\FollowWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //被关注，添加积分并发送通知
        \Hifone\Events\Follow\FollowedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        \Hifone\Events\Follow\FollowedWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        //精华
        \Hifone\Events\Excellent\ExcellentWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        // Image

        \Hifone\Events\Image\ImageWasUploadedEvent::class => [
            \Hifone\Handlers\Listeners\Photo\AddPhotoRecordHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        // 按赞
        \Hifone\Events\Thread\ThreadWasLikedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        \Hifone\Events\Like\LikeWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        \Hifone\Events\Like\LikeWasRemovedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        \Hifone\Events\Like\LikedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
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

        // 回帖
        \Hifone\Events\Reply\ReplyWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendReplyNotificationHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        \Hifone\Events\Reply\RepliedWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //置顶
        \Hifone\Events\Pin\PinWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //下沉
        \Hifone\Events\Pin\SinkWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //发表帖子
        \Hifone\Events\Thread\ThreadWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendThreadNotificationHandler::class,
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //上传头像
        \Hifone\Events\Image\AvatarWasUploadedEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],
        //
        \Hifone\Events\Thread\ThreadWasMarkedExcellentEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],
        //
        \Hifone\Events\Thread\ThreadWasPinnedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
        ],

        //
        \Hifone\Events\Thread\ThreadWasMovedEvent::class => [
            \Hifone\Handlers\Listeners\Notification\SendSingleNotificationHandler::class,
            \Hifone\Handlers\Listeners\Thread\UpdateThreadNodesHandler::class,
        ],

        \Hifone\Events\Thread\ThreadWasViewedEvent::class => [
            \Hifone\Handlers\Listeners\Thread\UpdateThreadViewCountHandler::class,
        ],

        \Hifone\Events\Banner\BannerWasViewedEvent::class => [
            \Hifone\Handlers\Listeners\Banner\UpdateBannerViewCountHandler::class,
        ],

        \Hifone\Events\User\UserWasAddedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateStatsHandler::class,
            \Hifone\Handlers\Listeners\Identity\ChangeUsernameHandler::class,
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
        ],

        \Hifone\Events\User\UserWasLoggedinEvent::class => [
            \Hifone\Handlers\Listeners\Credit\AddCreditHandler::class,
            \Hifone\Handlers\Listeners\User\UserWasLoggedInHandler::class,
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

        //帖子审核通过
        \Hifone\Events\Thread\ThreadWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
        ],
        //回复审核通过
        \Hifone\Events\Reply\ReplyWasAuditedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
        ],
        //帖子移入垃圾箱
        \Hifone\Events\Thread\ThreadWasTrashedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
        ],
        //回复移入垃圾箱
        \Hifone\Events\Reply\ReplyWasTrashedEvent::class => [
            \Hifone\Handlers\Listeners\Stats\UpdateDailyStatsHandler::class,
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
