<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Models\Thread;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Request;
use Input;

class ThreadController extends AbstractApiController
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Get all Threads.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $threads = Thread::visible()->with('user')->orderBy('id', 'desc')->paginate(Input::get('per_page', 20));

        return $threads;
    }

    public function show(Thread $thread)
    {
        return Thread::with('user', 'replies', 'replies.user')->find($thread->id);
    }
}
