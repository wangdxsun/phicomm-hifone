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

use Hifone\Jobs\Notify;
use Hifone\Models\User;

class Notifier
{
    protected $notifiedUsers = [];

    public function notify($type, User $author, User $toUser, $object)
    {
        dispatch(new Notify($type, $author, $toUser, $object));
    }

    public function batchNotify($type, User $author, $users, $object, $content = null)
    {
        foreach ($users as $user) {
            $user = $user instanceof User ? $user : $user->user;
//            dispatch((new Notify($type, $author, $user, $object))->onQueue('low'));
            dispatch((new Notify($type, $author, $user, $object)));
        }
    }
}
