<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\CreditRule;
use Redirect;
use View;
use Input;

class CreditController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'credit',
            'sub_title'     => '经验值管理',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $creditRules = CreditRule::all();

        return View::make('dashboard.credit.index')
            ->withPageTitle('经验值管理')
            ->withCreditRules($creditRules);
    }

    public function edit(CreditRule $creditRule)
    {
        return View::make('dashboard.credit.edit')
            ->withPageTitle('修改经验值规则 - '.trans('dashboard.dashboard'))
            ->withCreditRule($creditRule);
    }

    public function update(CreditRule $creditRule)
    {
        $creditRuleData = Input::get('creditRule');
        try {
            $creditRule->update($creditRuleData);
        } catch (\Exception $e) {
            return Redirect::route('dashboard.creditRule.edit', ['id' => $creditRule->id])
                ->withTitle('经验值规则修改失败')
                ->withErrors($e->getMessage());
        }
        return Redirect::back()
            ->withSuccess('经验值规则修改成功');
    }
}
