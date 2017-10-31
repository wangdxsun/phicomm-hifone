<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/27
 * Time: 15:51
 */

namespace Hifone\Test\Api;

use Hifone\Models\User;

class UserTest extends ApiTestCase
{
    public function testSearch()
    {
        $this->get('/user/search?q=江');
        $this->seeJsonStructure([
            '*' => ['id', 'username', 'avatar_url', 'role', 'follower_count', 'followed', 'search' => ['username']]
        ]);
    }

    public function testCurrentUser()
    {
        $this->get('/user/me');
        $this->seeJsonStructure([
            'id', 'username', 'avatar_url', 'role', 'follower_count', 'follow_count', 'thread_count', 'reply_count', 'score'
        ]);
    }
}