<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/27
 * Time: 15:51
 */

namespace Hifone\Test\Api;

class UserTest extends AbstractApiTestCase
{
    public function testSearch()
    {
        $this->get('/user/search?q=江');
        $this->seeJsonStructure([
            '*' => ['id', 'username', 'avatar_url', 'role', 'follower_count', 'followed', 'search' => ['username']]
        ]);
    }
}