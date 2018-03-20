@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
<div class="header sub-header">
    <span class="uppercase">
        <i class="ion ion-ios-information-outline"></i> {{ trans('dashboard.links.links') }}
    </span>
    <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.link.create') }}">
        {{ trans('dashboard.links.add.title') }}
    </a>
    <div class="clearfix"></div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="striped-list" id="item-list" data-item-name="link">
            @forelse($links as $link)
            <div class="row striped-list-item" data-item-id="{{ $link->id }}">
                <div class="col-xs-1">
                    <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                </div>
                <div class="col-md-4">
                    <p><a href="{{ $link->url }}" target="_blank">{{ $link->title }}</a></p>
                </div>
                <div class="col-md-4">
                    <p><small>{{ $link->description }}</small></p>
                </div>
                <div class="col-md-3 text-right">
                    <a href="{{ route('dashboard.link.edit',['id'=>$link->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                    <a data-url="{{ route('dashboard.link.destroy',['id'=>$link->id]) }}" class="btn btn-danger btn-sm confirm-action" data-method='delete'>{{ trans('forms.delete') }}</a>
                </div>
            </div>
            @empty
            <div class="list-group-item text-danger">{{ trans('dashboard.links.add.message') }}</div>
            @endforelse
        </div>
    </div>
</div>
@stop