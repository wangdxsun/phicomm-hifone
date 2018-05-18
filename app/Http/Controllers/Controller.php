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
use Carbon\Carbon;
use Hifone\Models\BaseModel;
use Hifone\Services\Breadcrumb\Breadcrumb;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Zizaco\Entrust\EntrustFacade as Entrust;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->breadcrumb = new Breadcrumb();
    }

    /**
     * Renders view with breadcrumb.
     *
     * @param string|null $view
     * @param array       $data
     *
     * @return \Illuminate\View\View
     */
    protected function view($view = null, $data = [])
    {
        if (count($this->breadcrumb)) {
            $data['breadcrumb'] = $this->breadcrumb->render();
        }

        return View::make($view, $data);
    }

    public function needAuthorOrAdminPermission($author_id)
    {
        if (!Entrust::hasRole(['Founder', 'Admin']) && $author_id != Auth::id()) {
            throw new HttpException(401);
        }
    }

    public function filterEmptyValue($input)
    {
        $input = $input ?: [];
        return array_filter($input, function($value) {
            return $value != '';
        });
    }

    public function updateOpLog(BaseModel $model, $operation, $reason = null)
    {
        $operator = $operation == '自动审核通过' ? 0 : Auth::id();
        $model->last_op_user_id = $operator;
        $model->last_op_time = Carbon::now()->toDateTimeString();
        $reason && $model->last_op_reason = $reason;
        $model->save();
        $logData['user_id'] = $operator;
        $logData['operation'] = $operation;
        $logData['reason'] = $reason;
        $model->logs()->create($logData);
    }

    protected function makeMixedContent($bodies)
    {
        $content = '';
        foreach ($bodies as $body) {
            if ($body['type'] == 'text') {
                $content.= "<p>".e($body['content'])."</p>";
            } elseif ($body['type'] == 'image') {
                $content.= "<img src='".$body['content']."'/>";
            }
        }

        return $content;
    }
}
