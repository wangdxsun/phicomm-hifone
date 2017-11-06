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

class TestController extends Controller
{
    public function test()
    {
        dd(session()->getId(), csrf_token());
    }
}