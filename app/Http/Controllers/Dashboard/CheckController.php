<?php

namespace Hifone\Http\Controllers\Dashboard;

use Auth;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Permission;


class CheckController extends  Controller
{
    public function check() {
       $permission = Permission::userGroup()->get();
       dd($permission->toArray());
    }
}
