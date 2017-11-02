<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/23
 * Time: 16:50
 */

namespace Hifone\Test\Api;

class GeneralTest extends ApiTestCase
{
    public function testPing()
    {
        $this->get('/ping');
        $this->see('Pong!');
    }

    public function testException()
    {
        $this->get('/exception');
        $this->seeJson(['msg' => 'myException']);
    }
}