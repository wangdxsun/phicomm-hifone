<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;

class CheckController extends  Controller
{
    public function check() {
        return view('dashboard.test');
    }
}
