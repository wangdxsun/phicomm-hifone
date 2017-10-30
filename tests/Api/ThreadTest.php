<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/26
 * Time: 10:48
 */

namespace Hifone\Test\Api;

class ThreadTest extends AbstractApiTestCase
{
    public function testThreads()
    {
        $this->get('/threads');
        $this->seeJsonStructure([
            'next_page_url',
            'data' => [
                '*' => ['id', 'title', 'body', 'thumbnails', 'user' => [
                    'id', 'username', 'avatar_url'
                ], 'node' => [
                    'id', 'name', 'description', 'thread_count', 'icon', 'icon_detail', 'icon_list'
                ]],
            ],
        ]);
    }

    public function testSearch()
    {
        $this->get('/thread/search?q=江');
        $this->seeJsonStructure([
            '*' => ['id', 'title', 'body', 'thumbnails', 'user' => [
                'id', 'username', 'avatar_url'
            ], 'node' => [
                'id', 'name', 'description', 'thread_count', 'icon', 'icon_detail', 'icon_list'
            ]],
        ]);
    }
}