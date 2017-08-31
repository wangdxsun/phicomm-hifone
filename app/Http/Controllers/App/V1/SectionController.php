<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 16:21
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Section;

class SectionController extends AppController
{
    public function index()
    {
        return Section::with('nodes')->get();
    }
}