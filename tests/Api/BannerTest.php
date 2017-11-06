<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/11/1
 * Time: 8:42
 */

namespace Hifone\Test\Api;

class BannerTest extends ApiTestCase
{
    public function testBanners()
    {
        $this->get('/banners');
        $this->seeJsonStructure([
            '*' => ['image', 'statistic']
        ]);
    }
}