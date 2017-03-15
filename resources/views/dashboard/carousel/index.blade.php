@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">
        <span class="uppercase"><i class="ion ion-ios-browsers-outline"></i> 轮播图管理</span>
        <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.carousel.create') }}">添加轮播图</a>
        <div class="clearfix"></div>
    </div>
    @include('partials.errors')
    <div class="row">
        <div class="col-sm-12 striped-list" id="item-list" data-item-name="carousel">
            @forelse($carousels as $carousel)
            <div class="row striped-list-item" data-item-id="{{ $carousel->id }}">
                <div class="col-xs-1">
                    <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                </div>
                <div class="col-xs-4">
                    <a href="{{ $carousel->url }}"><img src="{{ $carousel->image }}" style="max-width: 200px; max-height: 50px;"></a>
                </div>
                <div class="col-xs-4">{{ $carousel->description }}</div>
                <div class="col-xs-3 text-right">
                    <a href="{{ route('dashboard.carousel.edit',['id'=>$carousel->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                    <a data-url="{{ route('dashboard.carousel.destroy',['id'=>$carousel->id]) }}" class="btn btn-danger btn-sm confirm-action" data-method="delete">{{ trans('forms.delete') }}</a>
                </div>
            </div>
            @empty
            <div class="list-group-item text-danger">当前没有轮播图</div>
            @endforelse
        </div>
    </div>
</div>
@stop
