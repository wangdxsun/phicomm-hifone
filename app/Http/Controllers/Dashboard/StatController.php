<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/6/14
 * Time: 14:02
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Carousel;


class StatController extends Controller
{
    public function index()
    {
        return view('dashboard.stats.index')->withCurrentMenu('index');
    }

    public function banner()
    {
        $carousels = Carousel::recent()->paginate(10);
        return view('dashboard.stats.banner')->withCurrentMenu('banner')->withCarousels($carousels);
    }

    public function node()
    {
        return view('dashboard.stats.node')->withCurrentMenu('node');
    }

    public function banner_detail(Carousel $carousel)
    {
        $dailyBanners = $carousel->dailyBanners;
        return view('dashboard.stats.banner_detail')->withCurrentMenu('banner_detail')->withDailyBanners($dailyBanners);
    }
}