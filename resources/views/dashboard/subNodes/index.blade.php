@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
    @include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">
        <div class="header sub-header">
            <span class="uppercase"><i class="ion ion-ios-browsers-outline"></i>子版块管理</span>
            <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.subNode.create') }}">
                {{ trans('dashboard.nodes.add.sub_title') }}
            </a>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 striped-list" id="item-list" data-item-name="subNode">
            <div class="row striped-list-item">
                <div class="col-xs-1"><span>排序</span></div>
                <div class="col-xs-2"><span>编号</span></div>
                <div class="col-xs-2"><span>子板块</span></div>
                <div class="col-xs-2"><span>主板块</span></div>
                <div class="col-xs-3"><span>子板块描述</span></div>
                <div class="col-xs-2"><span>操作</span></div>
            </div>
            @forelse($subNodes as $subNode)
                <div class="row striped-list-item" data-item-id="{{ $subNode->id }}">
                    <div class="col-xs-1">
                        <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                    </div>
                    <div class="col-xs-2 drag-handle" >
                        {{ $subNode->id }}
                    </div>
                    <div class="col-xs-2 drag-handle" >
                        {{ $subNode->name }}
                    </div>
                    <div class="col-xs-2 drag-handle">
                        {{ $subNode->node->name }}
                    </div>
                    <div class="col-xs-3 drag-handle">
                        {{ $subNode->description }}
                    </div>
                    <div class="col-xs-2 drag-handle">
                        <a href="{{ route('dashboard.subNode.edit',['id'=>$subNode->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                        <a data-url="{{ route('dashboard.subNode.destroy',['id'=>$subNode->id]) }}" class="btn btn-danger btn-sm confirm-action" data-method="delete">{{ trans('forms.delete') }}</a>
                    </div>
                </div>
            @empty
                <div class="list-group-item"><a href="{{ route('dashboard.subNode.create') }}">{{ trans('dashboard.nodes.add.message') }}</a></div>
            @endforelse
        </div>
    </div>
</div>
@stop