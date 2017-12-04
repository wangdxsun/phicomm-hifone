<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Services\Notifier;

use Carbon\Carbon;
use Hifone\Models\Notification;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;

class Notifier
{
    protected $notifiedUsers = [];

    public function notify($type, User $author, User $toUser, $object)
    {
        if ($this->isNotified($author->id, $toUser->id, $object, $type)) {
            return;
        }

        $nowTimestamp = Carbon::now()->toDateTimeString();

        $data = [
            'author_id'     => $author->id,
            'user_id'       => $toUser->id,
            'body'          => ($object instanceof Thread) ? '': (isset($object->body) ? $object->body : ''),
            'type'          => $type,
            'created_at'    => $nowTimestamp,
            'updated_at'    => $nowTimestamp,
        ];

        if($type != 'thread_favorite' && $type != 'thread_mark_excellent' && $type != 'thread_pin' && $type != 'thread_follow' ) {
            $toUser->increment('notification_system_count', 1);
            $toUser->increment('notification_count', 1);
        }

        $object->notifications()->create($data);
    }

    public function batchNotify($type, User $author, $users, $object, $content = null)
    {
        $nowTimestamp = Carbon::now()->toDateTimeString();

        foreach ($users as $follower) {
            $toUser = (!$follower instanceof User) ? $follower->user : $follower;

            if (empty($toUser)|| empty($author) || $author->id == $toUser->id) {
                continue;
            }

            $data = [
                'author_id'     => $author->id,
                'user_id'       => $toUser->id,
                'body'          => $content,
                'type'          => $type,
                'created_at'    => $nowTimestamp,
                'updated_at'    => $nowTimestamp,
            ];

            $object->notifications()->create($data);
            $toUser->increment('notification_count', 1);
        }
    }

    protected function isNotified($author_id, $user_id, $object, $type)
    {
        return $object->notifications()
                    ->forAuthor($author_id)
                    ->forUser($user_id)
                    ->ofType($type)->count();
    }

    // in case of a user get a lot of the same notification
    protected function removeDuplication($users)
    {
        $notYetNotifyUsers = [];
        foreach ($users as $follower) {
            $toUser = (!$follower instanceof User) ? $follower->user : $follower;

            if (!in_array($toUser->id, $this->notifiedUsers)) {
                $notYetNotifyUsers[] = $toUser;
                $this->notifiedUsers[] = $toUser->id;
            }
        }

        return $notYetNotifyUsers;
    }
}
