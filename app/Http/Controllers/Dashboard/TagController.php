<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use View;

class TagController extends Controller
{

    public function __construct()
    {
        View::share([
            'current_menu'  => 'users',
        ]);
    }
    public function index()
    {
        return View::make('dashboard.tags.index');

    }
}