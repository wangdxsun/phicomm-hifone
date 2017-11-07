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
use Hifone\Models\User;
use Illuminate\Support\Facades\Input;
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
        $logableTypes = Log::$logableType;//操作对象
        $operations = Log::distinct()->get(['operation']);//操作类型
        $userIds = Log::distinct()->get(['user_id'])->toArray();//用户id
        $search = $this->filterEmptyValue(Input::get('log'));

        $logs = Log::with(['user'])->search($search)->orderBy('created_at', 'desc')->paginate(20);
        return view('dashboard.logs.index')
            ->withLogs($logs)
            ->withLogableTypes($logableTypes)
            ->withSearch($search)
            ->withOperations($operations)
            ->withUsers(User::find($userIds));
    }

}
