<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/6/14
 * Time: 14:02
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\DailyStat;

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
        $nodes = Node::orderBy('order')->get();
        return view('dashboard.stats.node')->withCurrentMenu('node')->withNodes($nodes);
    }

    public function node_detail(Node $node)
    {
        $dailyNodes = $node->dailyStats;
        return view('dashboard.stats.node_detail')->withCurrentMenu('node_detail')->withDailyNodes($dailyStats);
    }
}