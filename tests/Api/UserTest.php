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

    public function testPhicommNoLoginUser()
    {
        $this->get('/user/me', ['Authorization' => '']);
        $this->see('PhicommNoLogin');
    }

//    public function testUnbindUser()
//    {
//        User::destroy(1755);
//        $this->get('/user/me');
//        $this->see('Unbind');
//    }

    public function testUserDetail()
    {
        $this->get('/users/'.$this->user->id);
        $this->seeJsonStructure([
            'id', 'username', 'avatar_url', 'role', 'follower_count', 'follow_count', 'thread_count', 'reply_count', 'score'
        ]);
    }

    public function testUserFollows()
    {
        $this->get('/users/'.$this->user->id.'/follows');
        $this->seeJsonStructure([
            'next_page_url', 'data' => [
                '*' => ['followed', 'follower' => ['id', 'username', 'avatar_url']]
            ]
        ]);
    }

    public function testUserFollowers()
    {
        $this->get('/users/'.$this->user->id.'/followers');
        $this->seeJsonStructure([
            'next_page_url', 'data' => [
                '*' => ['followed', 'user' => ['id', 'username', 'avatar_url']]
            ]
        ]);
    }
}