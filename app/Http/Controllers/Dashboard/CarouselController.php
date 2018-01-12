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

use AltThree\Validator\ValidationException;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Carousel;
use Hifone\Models\Thread;
use \Redirect;
use \Request;
use \View;

class CarouselController extends Controller
{
    /**
     * Creates a new notice controller instance.
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'carousel',
        ]);
    }
    /**
     * app端正在展现的banner
     *
     */
    public function index()
    {
        $carousels  = Carousel::orderBy('order')->recent()->whereIn('device', [4,8,12])->visible()->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withSrc('app')
            ->withCurrentMenu('app')
            ->withCurrentTap('app_show');
    }

    public function appShow()
    {
        $carousels  = Carousel::orderBy('order')->recent()->whereIn('device', [4,8,12])->visible()->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withSrc('app')
            ->withCurrentMenu('app')
            ->withCurrentTap('app_show');

    }

    public function appHideBanners()
    {
        $carousels  = Carousel::orderBy('order')->recent()->whereIn('device', [4,8,12])->hide()->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withSrc('app')
            ->withCurrentMenu('app')
            ->withCurrentTap('app_hide');

    }

    public function webShow()
    {
        $carousels  = Carousel::orderBy('order')->recent()->whereIn('device', [1,2,3])->visible()->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withSrc('web')
            ->withCurrentMenu('web')
            ->withCurrentTap('web_show');
    }

    public function webHideBanners()
    {
        $carousels  = Carousel::orderBy('order')->recent()->whereIn('device', [1,2,3])->hide()->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withSrc('web')
            ->withCurrentMenu('web')
            ->withCurrentTap('web_hide');

    }

    public function hideBanners()
    {
        $carousels  = Carousel::orderBy('order')->recent()->where('visible', 0)->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withCurrentMenu('hide');
    }

    public function show(Carousel $carousel)
    {
        return redirect($carousel->jump_url);
    }


    //web、h5端添加banner
    public function create()
    {
        return View::make('dashboard.carousel.create_edit')
            ->withPageTitle('添加banner')
            ->withCurrentMenu('web');
    }

    /**
     * app端添加banner
     */
    public function createApp()
    {
        return View::make('dashboard.carousel.app_create_edit')
            ->withPageTitle('添加banner')
            ->withCurrentMenu('app');
    }

    /**
     *
     */
    public function store()
    {
        $carouselData = Request::get('carousel');
        if ( $carouselData['h5_icon'] == "" && $carouselData['web_icon'] == "") {
            return Redirect::back()->withErrors('请至少上传一张图片');
        } elseif($carouselData['h5_icon'] != "" && $carouselData['web_icon'] != "") {
            $carouselData['device'] = 3;
        } else {
            $carouselData['device'] = $carouselData['h5_icon'] != "" ?  1 : 2;
        }
        $carouselData['image'] = $carouselData['h5_icon'] != "" ?  $carouselData['h5_icon'] : $carouselData['web_icon'];

        if ($carouselData['type'] == 1) {
            $thread_id = $carouselData['url'];
            $thread = Thread::visible()->find($thread_id);
            if (!$thread) {
                return Redirect::back()->withErrors('您所配置的帖子不可见或不存在');
            }
        }
        try {
            $carousel = Carousel::create($carouselData);
            $this->updateOpLog($carousel, '添加banner');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.carousel.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.carousel.web.show')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.add.success')));
    }

    public function storeApp()
    {
        $carouselData = Request::get('carousel');

        if ( $carouselData['android_icon'] == "" && $carouselData['ios_icon'] == "") {
            return Redirect::back()->withErrors('请至少上传一张图片');
        } elseif($carouselData['android_icon'] != "" && $carouselData['ios_icon'] != "") {
            $carouselData['device'] = 12;
        } else {
            $carouselData['device'] = $carouselData['android_icon'] != "" ?  4 : 8;
        }
        $carouselData['image'] = $carouselData['android_icon'] != "" ?  $carouselData['android_icon'] : $carouselData['ios_icon'];

        if (empty($carouselData['version'])) {
            return Redirect::route('dashboard.carousel.create.app')
                ->withErrors('没有选择版本类型')
                ->withInput();
        } elseif ($carouselData['version'] == 1) {
            $carouselData['start_version'] = '全部版本';
        } elseif ($carouselData['version'] == 2) {
            if ($carouselData['start_version'] == "" || $carouselData['end_version'] == "") {
                return Redirect::route('dashboard.carousel.create.app')
                    ->withErrors('自定义版本请选择起止版本号')
                    ->withInput();
            }

        } else {
            return Redirect::route('dashboard.carousel.create.app')
                ->withErrors('版本选择错误')
                ->withInput();
        }

        if ($carouselData['type'] == 1) {
            $thread_id = $carouselData['url'];
            $thread = Thread::visible()->find($thread_id);
            if (!$thread) {
                return Redirect::back()->withErrors('您所配置的帖子不可见或不存在');
            }
        } else {
            $linkUrl = $carouselData['url'];
            if ((string)($linkUrl + 0) == $linkUrl) {//链接输入为纯数字
                return Redirect::back()->withErrors('您所配置的跳转链接有误');
            }
        }
        try {
            $carousel = Carousel::create($carouselData);
            $this->updateOpLog($carousel, '添加banner');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.carousel.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.carousel.app.show')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.add.success')));
    }

    public function edit(Carousel $carousel)
    {
        return View::make('dashboard.carousel.create_edit')
            ->withPageTitle('编辑banner')
            ->withCarousel($carousel)
            ->withCurrentMenu('web');
    }

    public function editApp(Carousel $carousel)
    {
        return View::make('dashboard.carousel.app_create_edit')
            ->withPageTitle('编辑banner')
            ->withCarousel($carousel)
            ->withCurrentMenu('app');
    }

    public function update(Carousel $carousel)
    {
        $carouselData = Request::get('carousel');
        if ( $carouselData['h5_icon'] == "" && $carouselData['web_icon'] == "") {
            return Redirect::back()->withErrors('请至少上传一张图片');
        } elseif($carouselData['h5_icon'] != "" && $carouselData['web_icon'] != "") {
            $carouselData['device'] = 3;
        } else {
            $carouselData['device'] = $carouselData['h5_icon'] != "" ?  1 : 2;
        }
        $carouselData['image'] = $carouselData['h5_icon'] != "" ?  $carouselData['h5_icon'] : $carouselData['web_icon'];
        if ($carouselData['type'] == 1) {
            $thread_id = $carouselData['url'];
            $thread = Thread::visible()->find($thread_id);
            if (!$thread) {
                return Redirect::back()->withErrors('您所配置的帖子不可见或不存在');
            }
        } else {
            $linkUrl = $carouselData['url'];
            if ((string)($linkUrl + 0) == $linkUrl) {//链接输入为纯数字
                return Redirect::back()->withErrors('您所配置的跳转链接有误');
            }
        }
        try {
            $carousel->update($carouselData);
            $this->updateOpLog($carousel, '修改banner');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.carousel.edit', ['id' => $carousel->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.carousel.web.show')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.edit.success')));
    }

    public function updateApp(Carousel $carousel)
    {
        $carouselData = Request::get('carousel');
        if ( $carouselData['android_icon'] == "" && $carouselData['ios_icon'] == "") {
            return Redirect::back()->withErrors('请至少上传一张图片');
        } elseif($carouselData['android_icon'] != "" && $carouselData['ios_icon'] != "") {
            $carouselData['device'] = 12;
        } else {
            $carouselData['device'] = $carouselData['android_icon'] != "" ?  4 : 8;
        }
        $carouselData['image'] = $carouselData['android_icon'] != "" ?  $carouselData['android_icon'] : $carouselData['ios_icon'];

        if (empty($carouselData['version'])) {
            return Redirect::route('dashboard.carousel.create.app')
                ->withErrors('没有选择版本类型')
                ->withInput();
        } elseif ($carouselData['version'] == 1) {
            $carouselData['start_version'] = '全部版本';
        } elseif ($carouselData['version'] == 2) {
            if ($carouselData['start_version'] == "" || $carouselData['end_version'] == "") {
                return Redirect::route('dashboard.carousel.create.app')
                    ->withErrors('自定义版本请选择起止版本号')
                    ->withInput();
            }

        } else {
            return Redirect::route('dashboard.carousel.create.app')
                ->withErrors('版本选择错误')
                ->withInput();
        }

        if ($carouselData['type'] == 1) {
            $thread_id = $carouselData['url'];
            $thread = Thread::visible()->find($thread_id);
            if (!$thread) {
                return Redirect::back()->withErrors('您所配置的帖子不可见或不存在');
            }
        }
        try {
            $carousel->update($carouselData);
            $this->updateOpLog($carousel, '修改banner');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.carousel.edit.app', ['id' => $carousel->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.carousel.app.show')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.edit.success')));
    }

    public function destroy(Carousel $carousel)
    {
        $this->updateOpLog($carousel, '删除banner');
        $carousel->delete();

        return Redirect::route('dashboard.carousel.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function close(Carousel $carousel)
    {
        if ($carousel->visible > 0) {
            $carousel->visible -= 1;
            $this->updateOpLog($carousel, '关闭banner');
        } else {
            $carousel->visible += 1;
            $this->updateOpLog($carousel, '开启banner');
        }

        return Redirect::back()->withSuccess('修改成功！');
    }
}