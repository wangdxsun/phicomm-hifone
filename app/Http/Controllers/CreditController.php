<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use Auth;
use Config;
use Hifone\Http\Bll\CreditBll;
use Illuminate\Support\Facades\View;

class CreditController extends Controller
{
    public function index(CreditBll $creditBll)
    {
        $credits = $creditBll->getCredits();

        return $this->view('credits.index')
            ->withCredits($credits);
    }
}
