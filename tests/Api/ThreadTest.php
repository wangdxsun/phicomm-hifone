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
        $this->get('/api/v1/threads');
        $this->seeJson([]);
    }
}