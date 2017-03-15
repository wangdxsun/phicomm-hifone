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
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Input;

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
        $carousels  = Carousel::orderBy('order')->get();

        return View::make('dashboard.carousel.index')
            ->withPageTitle('轮播管理')
            ->withCarousels($carousels);
    }

    /**
     * Shows the create carousel view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.carousel.create_edit')
            ->withPageTitle('添加轮播图');
    }

    /**
     * Stores a new carousel.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {

        $carouselData = Request::get('carousel');
        try {
            Carousel::create($carouselData);
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
            ->withPageTitle('编辑轮播图')
            ->withCarousel($carousel);
    }

    public function update(Carousel $carousel)
    {
        $carouselData = Request::get('carousel');
        try {
            $carousel->update($carouselData);
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
        $carousel->delete();;

        return Redirect::route('dashboard.carousel.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}