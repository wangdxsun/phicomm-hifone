@extends('layouts.dashboard')

@section('content')

<div class="content-wrapper">
    <div class="header sub-header">
        <i class="ion ion-ios-browsers-outline"></i> banner管理
        <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.carousel.create') }}">添加banner</a>
        <div class="clearfix"></div>
    </div>
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-nav')
    @endif
    <div class="row">
        @include('partials.errors')
        <div class="col-sm-12 striped-list" id="item-list" data-item-name="carousel">
            @forelse($carousels as $carousel)
            <div class="row striped-list-item" data-item-id="{{ $carousel->id }}">
                <div class="col-xs-1">
                    <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                </div>

                <div class="col-xs-1">
                    <span class="drag-handle">{{ $carousel->id  }}</span>
                </div>

                <div class="col-xs-3">
                    <a href="{{ $carousel->url }}" target="_blank"><img src="{{ $carousel->image }}" style="max-width: 200px; max-height: 50px;"></a>
                </div>
                <div class="col-xs-3">{{ $carousel->description }}<br><a href="{{ $carousel->url }}" target="_blank">{{ $carousel->jump_url }}</a></div>
                <div class="col-xs-2">
                    <div>{!! $carousel->user->username.'<br>'.$carousel->updated_time !!}  </div>
                </div>
                <div class="col-xs-2 text-right">
                    <a href="{{ route('dashboard.carousel.edit',['id'=>$carousel->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                    <a data-url="{{ route('dashboard.carousel.close',['id'=>$carousel->id]) }}" class="btn btn-primary btn-sm" data-method="post">{{ $carousel->visible ? '关闭' : '打开' }}</a>
                </div>
            </div>
            @empty
                <div class="list-group-item text-danger">当前没有banner</div>
            @endforelse
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <img src="" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
