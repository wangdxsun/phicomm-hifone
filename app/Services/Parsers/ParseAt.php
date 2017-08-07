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
        $this->usernames = $this->getUsernames();

        count($this->usernames) > 0 && $this->users = User::whereIn('username', $this->usernames)->get();

        $this->replace();

        return $this->body;
    }

    protected function replace()
    {
        foreach ($this->users as $user) {
            $search = '@'.$user->username;
            $place = "<a href='/user/{$user->id}'>$search</a>";

            $this->body = str_replace($search, $place, $this->body);
        }
    }

    protected function getUsernames()
    {
        preg_match_all("/\@([^@<\r\n\s]*)/i", $this->body, $atlist_tmp);
        $usernames = [];

        foreach ($atlist_tmp[1] as $k => $v) {
            if (strlen($v) > 25) {
                continue;
            }
            $usernames[] = $v;
        }

        return array_unique($usernames);
    }

}
