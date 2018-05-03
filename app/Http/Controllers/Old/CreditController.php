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

use Hifone\Http\Bll\CreditBll;
use Hifone\Http\Bll\UserBll;

class CreditController extends Controller
{
    public function index(UserBll $userBll)
    {
        $credits = $userBll->getCredits();

        return $this->view('credits.index')
            ->withCredits($credits);
    }
}
