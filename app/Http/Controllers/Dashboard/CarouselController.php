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
     * Shows the carousels view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $carousels  = Carousel::orderBy('order')->where('visible', 1)->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withCurrentMenu('index');
    }

    public function hideBanners()
    {
        $carousels  = Carousel::orderBy('order')->where('visible', 0)->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('banner管理')
            ->withCarousels($carousels)
            ->withCurrentMenu('hide');
    }


    public function show(Carousel $carousel)
    {
        return redirect($carousel->jump_url);
    }

    /**
     * Shows the create carousel view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.carousel.create_edit')
            ->withPageTitle('添加banner');
    }

    /**
     * Stores a new carousel.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $carouselData = Request::get('carousel');

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

        return Redirect::route('dashboard.carousel.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.notices.add.success')));
    }

    public function edit(Carousel $carousel)
    {
        return View::make('dashboard.carousel.create_edit')
            ->withPageTitle('编辑banner')
            ->withCarousel($carousel);
    }

    public function update(Carousel $carousel)
    {
        $carouselData = Request::get('carousel');
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
            return Redirect::route('dashboard.carousel.edit', ['id' => $carousel->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.notices.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.carousel.index', ['id' => $carousel->id])
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