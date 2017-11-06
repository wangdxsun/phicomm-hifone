<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/26
 * Time: 10:54
 */

namespace Hifone\Test\Api;

class NodeTest extends ApiTestCase
{
    public function testNodes()
    {
        $this->get('/nodes');
        $this->seeJsonStructure([
            '*' => ['id', 'name', 'description', 'thread_count', 'icon', 'icon_detail', 'icon_list']
        ]);
    }

    public function testNodeDetail()
    {
        $this->get('/nodes/37');
        $this->seeJsonStructure([
            'id', 'name', 'description', 'thread_count', 'icon', 'icon_detail', 'icon_list', 'hot' => [
                'next_page_url', 'data' => [
                    '*' => ['id', 'title', 'body', 'thumbnails', 'user' => [
                        'id', 'username', 'avatar_url'
                    ], 'sub_node' => [
                        'id', 'name'
                    ]],
                ]
            ], 'recent' => [
                'next_page_url', 'data' => [
                    '*' => ['id', 'title', 'body', 'thumbnails', 'user' => [
                        'id', 'username', 'avatar_url'
                    ], 'sub_node' => [
                        'id', 'name'
                    ]],
                ]
            ]
        ]);
    }

    public function testSections()
    {
        $this->get('/sections');
        $this->seeJsonStructure([
            '*' => ['id', 'name', 'nodes' => [
                '*' => ['id', 'name', 'description', 'thread_count', 'icon', 'icon_detail', 'icon_list']
            ]]
        ]);
    }

    public function testSubNodes()
    {
        $this->actingAs($this->admin)->get('/subNodes');
        $this->seeJsonStructure([
            '*' => ['id', 'name', 'nodes' => [
                '*' => ['id', 'name', 'description', 'thread_count', 'icon', 'icon_detail', 'icon_list', 'is_show',
                    'sub_nodes' => ['*' => ['id', 'name']]
                ]
            ]]
        ]);
        $this->seeJson(['is_show' => 0]);

        //对普通用户隐藏公告活动版块
        $this->actingAs($this->user)->get('/subNodes');
        $this->dontSeeJson(['is_show' => 0]);
    }
}