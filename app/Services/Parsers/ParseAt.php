<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Services\Parsers;

use Hifone\Models\User;
use Html;

class ParseAt
{
    public $users = [];
    public $usernames;
    public $body;

    public function parse($body)
    {
        $this->body = $body;
        $this->usernames = $this->getUserNames();

        count($this->usernames) > 0 && $this->users = User::whereIn('username', $this->usernames)->get();

        $this->replace();

        return $this->body;
    }

    protected function replace()
    {
        foreach ($this->users as $user) {
            $search = '@'.$user->username;
            $replace = "<a href='/user/{$user->id}'>$search</a> $1";//$1是为了把多替换的部分还原回去
            $this->body = preg_replace("/(?<!>)$search([@<\s&]+|$)/", $replace, $this->body);//([@<\s]+|$)是为了避免前缀相同的用户名被误替换
        }
    }

    protected function getUserNames()
    {
        preg_match_all("/@([^@<\s&]*)/i", $this->body, $names);
        $userNames = [];
        foreach ($names[1] as $name) {
            if (strlen($name) == 0 || mb_strlen($name) > 13 ) {
                continue;
            }
            $userNames[] = $name;
        }

        return array_unique($userNames);
    }

}
