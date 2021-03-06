<?php
namespace Hifone\Console\Commands;

use Hifone\Models\Thread;
use Illuminate\Console\Command;
use Str;

class GetThumbnails extends Command
{
    protected $signature = 'get:thumbnails';

    protected $description = 'get excellent users rank';

    public function __construct()
    {
        parent::__construct();
    }

    //根据被点赞数，被评论数和被收藏数筛选优质用户
    public function handle()
    {
        $threads= Thread::all();
        foreach ($threads as $thread) {
            if (Str::contains($thread->body_original,'<img')) {
                $thumbnails = getFirstImageUrl($thread->body_original)[0];
                $thread->thumbnails = $thumbnails;
                $thread->save();
            }
        }
        echo '获取所有的帖子的第一张图片地址并写入数据库成功！';
    }

}