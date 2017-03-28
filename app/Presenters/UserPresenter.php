<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Presenters;

use Hifone\Models\Role;

class UserPresenter extends AbstractPresenter
{
    public function url()
    {
        return route('user.home', $this->wrappedObject->username);
    }

    public function coins()
    {
        $divider = 2;

        $str = implode('', str_split(strrev($this->wrappedObject->score), $divider));

        $bronze = strrev(substr($str, 0, $divider));
        $silver = strrev(substr($str, 1 * $divider, $divider));
        $gold = strrev(substr($str, 2 * $divider));

        $coins = [
            'gold'   => $gold,
            'silver' => $silver,
            'bronze' => $bronze,
        ];

        $coins_str = '';
        foreach ($coins as $coin => $value) {
            if (!$value) {
                continue;
            }
            $coins_str .= '<img src="/images/'.$coin.'.png"> '.$value;
        }

        return $coins_str;
    }

    public function hasBadge()
    {
        $relations = Role::relationArrayWithCache();
        $user_ids = array_pluck($relations, 'user_id');

        return in_array($this->wrappedObject->id, $user_ids);
    }

    public function badgeName()
    {
        $relations = Role::relationArrayWithCache();
        $relation = array_first($relations, function ($key, $value) {
            return $value->user_id == $this->wrappedObject->id;
        });

        if (!$relation) {
            return '';
        }

        $roles = Role::rolesArrayWithCache();

        $role = array_first($roles, function ($key, $value) use (&$relation) {
            return $value->id == $relation->role_id;
        });

        return $role->display_name;
    }

    public function roles()
    {
        $adminGroup = implode('，', array_column($this->wrappedObject->roles->toArray(), 'display_name'));
        $userGroup = '未知用户组';
        $groups = Role::userGroup()->get();
        foreach ($groups as $group) {
            if ($this->wrappedObject->score >= $group->credit_low && $this->wrappedObject->score <= $group->credit_high) {
                $userGroup = $group->display_name;
            }
        }
        return $adminGroup ?: $userGroup;
    }

    /**
     * Convert the presenter instance to an array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return array_merge($this->wrappedObject->toArray(), [
            'created_at' => $this->created_at(),
            'updated_at' => $this->updated_at(),
        ]);
    }
}
