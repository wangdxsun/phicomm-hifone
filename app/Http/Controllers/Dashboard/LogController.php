<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Log;
use View;

class LogController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'log',
            'sub_title'     => '操作日志',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $logs = Log::with(['user'])->recent()->paginate(20);
        return view('dashboard.logs.index')->withLogs($logs);
    }

}
