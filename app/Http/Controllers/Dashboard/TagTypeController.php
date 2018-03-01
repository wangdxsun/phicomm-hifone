<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use View;

class TagTypeController extends Controller
{
    public function index()
    {
        return View::make('dashboard.tags.index');

    }

    public function create()
    {

    }
}