<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 21:03
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\CreditBll;

class CreditController extends ApiController
{
    public function index(CreditBll $creditBll)
    {
        $credits = $creditBll->getCredits();

        return $credits;
    }
}