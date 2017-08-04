<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2017/8/4
 * Time: 9:25
 */
namespace Hifone\Http\Controllers;
use Hifone\Models\Thread;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class GetFirstImgUrlController extends Controller
{
    public function index()
    {
        $threads= Thread::all();
        foreach ($threads as $thread) {
            if (Str::contains($thread->body_original,'<img')) {
                $thumbnails = $this->getFirstImageUrl($thread->body_original)[0];
                $thread->thumbnails = $thumbnails;
                $thread->save();
                unset($thumbnails);
            }
        }
        return Redirect::route('thread.index')
            ->withSuccess('获取所有的帖子的第一张图片的地址并写入数据库成功！');
    }

    public function getFirstImageUrl($body){
        preg_match_all('/src=["\']{1}([^"]*)["\']{1}/i', $body, $url_list_tmp);
        $imgUrls = [];

        foreach ($url_list_tmp[1] as $k => $v) {
            $imgUrls[] = $v;
        }
        return array_unique($imgUrls);
    }

}