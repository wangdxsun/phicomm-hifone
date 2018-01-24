@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
<div class="content-wrapper">
    <div class="header sub-header">
        <i class="ion ion-ios-browsers-outline"></i> banner管理
        <a class="btn btn-sm btn-success pull-right" href="{{ $current_menu == 'web' ? route('dashboard.carousel.create') : route('dashboard.carousel.create.app')}}">添加banner</a>
        <div class="clearfix"></div>
    </div>
    @if(isset($sub_nav))
        @include('dashboard.partials.sub_nav')
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
                    <span class="drag-handle">{{ 'ID : ' . $carousel->id  }}</span>
                </div>

                @if ($carousel->device == 4 || $carousel->device == 8 || $carousel->device == 12)
                    <div class="col-xs-2">
                        <a href="{{ $carousel->url }}" target="_blank"><img src="{{ $carousel->image != "" ? $carousel->image : ($carousel->ios_icon != "" ? $carousel->ios_icon : $carousel->android_icon)}}" style="max-width: 200px; max-height: 50px;"></a>
                    </div>
                @else
                    <div class="col-xs-2">
                        <a href="{{ $carousel->url }}" target="_blank"><img src="{{ $carousel->image != "" ? $carousel->image : ($carousel->h5_icon != "" ? $carousel->h5_icon : $carousel->web_icon)}}" style="max-width: 200px; max-height: 50px;"></a>
                    </div>
                @endif


                @if ($carousel->device == 4 || $carousel->device == 8 || $carousel->device == 12)
                    <div class="col-xs-2">{{ '描述 ：'  }}<br>{{ $carousel->description }}
                        <a href="{{ $carousel->url }}" target="_blank">{{ $carousel->jump_url }}</a>
                    </div>
                @else
                    <div class="col-xs-3">{{ '描述 ：'  }}<br>{{ $carousel->description }}
                        <a href="{{ $carousel->url }}" target="_blank">{{ $carousel->jump_url }}</a>
                    </div>
                @endif


                <div class="col-xs-2">
                    {{ '展现时间段 ： ' }}<br>{{ $carousel->start_display }}<br>{{ '~' }}<br>{{ $carousel->end_display }}
                </div>
                @if ($carousel->device == 4 || $carousel->device == 8 || $carousel->device == 12)
                    <div class="col-xs-1">
                        {{ '版本区间 ： ' }}<br>{{ $carousel->start_version == '全部版本' ? $carousel->start_version : $carousel->start_version. '~' . $carousel->end_version }}
                    </div>
                @endif
                <div class="col-xs-1">
                    {{ '适用系统 ： ' . $carousel->getDevice($carousel->device) }}<br>
                </div>

                <div class="col-xs-1">
                    <div>{{ $carousel->user->username }}<br>{{ $carousel->updated_time }}</div>
                </div>
                <div class="col-xs-1 text-right">
                    <a href="{{ $current_menu == 'web' ? route('dashboard.carousel.edit', ['id'=>$carousel->id]) : route('dashboard.carousel.edit.app', ['id'=>$carousel->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
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
