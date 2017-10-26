<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/23
 * Time: 16:50
 */

namespace Hifone\Test\Api;

class GeneralTest extends AbstractApiTestCase
{
    public function testPing()
    {
        $this->get('/api/v1/ping');
        $this->see('Pong!');
    }

    public function testException()
    {
        $this->get('/api/v1/exception');
        $this->seeJson(['msg' => 'myException']);
    }
}