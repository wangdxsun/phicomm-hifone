<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/27
 * Time: 15:18
 */

namespace Hifone\Test\Api;

use Hifone\Models\Thread;

class ReplyTest extends ApiTestCase
{
    public function testReplies()
    {
        $thread = Thread::hot()->first();
        $this->get('/threads/'.$thread->id.'/replies');
        $this->seeJsonStructure([
            'next_page_url',
            'data' => [
                '*' => ['id', 'body', 'like_count', 'created_at', 'liked', 'reply', 'user' => [
                    'id', 'username', 'avatar_url'
                ]]
            ],
        ]);
    }
}