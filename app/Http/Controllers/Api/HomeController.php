<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/4
 * Time: 11:07
 */

namespace Hifone\Http\Controllers\Api;

class HomeController extends AbstractApiController
{
    public function index()
    {
        return [
            '热门帖子列表' => $this->api('thread'),
            '帖子详情' => $this->api('thread/10'),
            '搜索帖子' => $this->api('thread?q=K2'),
        ];
    }

    private function api($url)
    {
        return url('/api/v1/' . $url);
    }
}