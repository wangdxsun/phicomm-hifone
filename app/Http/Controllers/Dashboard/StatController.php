<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/6/14
 * Time: 14:02
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;

class StatController extends Controller
{
    public function index()
    {
        return view('dashboard.stats.index')->withCurrentMenu('index');
    }

    public function banner()
    {
        return view('dashboard.stats.banner')->withCurrentMenu('banner');
    }

    public function node()
    {
        return view('dashboard.stats.node')->withCurrentMenu('node');
    }
}