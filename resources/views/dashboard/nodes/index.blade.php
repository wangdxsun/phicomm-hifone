@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">
        <span class="uppercase"><i class="ion ion-ios-browsers-outline"></i>主版块管理</span>
        <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.node.create') }}">
            {{ trans('dashboard.nodes.add.title') }}
        </a>
        <div class="clearfix"></div>
    </div>

    <div class="row">
        <div class="col-sm-12 striped-list" id="item-list" data-item-name="node">
            <div class="row striped-list-item">
                <div class="col-xs-1"><span>排序</span></div>
                <div class="col-xs-1"><span>编号</span></div>
                <div class="col-xs-2"><span>主板块</span></div>
                <div class="col-xs-2"><span>板块分类</span></div>
                <div class="col-xs-2"><span>板块描述</span></div>
                <div class="col-xs-2"><span>版主</span></div>
                <div class="col-xs-2"><span>操作</span></div>
            </div>
            @forelse($nodes as $node)
            <div class="row striped-list-item" data-item-id="{{ $node->id }}">
                <div class="col-xs-1">
                    <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                </div>
                <div class="col-xs-1">
                    <span>{{$node->id }}</span>
                </div>
                <div class="col-xs-2 drag-handle">
                    <a href="/dashboard/node/{{ $node->id }}">{{ $node->name }}</a>
                </div>
                <div class="col-xs-2 drag-handle">
                    <a href="/dashboard/section/{{ $node->section->id }}">{{ $node->section->name }}</a>
                </div>
                <div class="col-xs-2 drag-handle">
                    {{ $node->description }}
                </div>
                <div class="col-xs-2 ">
                    @foreach($node->moderators as $moderator)
                        @if (isset($moderator))
                            <td><a href="{{ route('user.show', ['id'=>$moderator->id]) }}" target="_blank">{{ $moderator->username }}</a></td><br>
                        @endif
                    @endforeach
                    @foreach($node->praModerators as $praModerator)
                        @if (isset($praModerator))
                            <td><a href="{{ route('user.show', ['id'=>$praModerator->id]) }}" target="_blank">{{ $praModerator->username }}</a></td><br>
                        @endif
                    @endforeach
                </div>

                <div class="col-xs-2 drag-handle">
                    <a href="{{ route('dashboard.node.edit',['id'=>$node->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                    <a data-url="{{ route('dashboard.node.destroy',['id'=>$node->id]) }}" class="btn btn-danger btn-sm confirm-action" data-method="delete">{{ trans('forms.delete') }}</a>
                </div>
            </div>
            @empty
            <div class="list-group-item"><a href="{{ route('dashboard.node.create') }}">{{ trans('dashboard.nodes.add.message') }}</a></div>
            @endforelse
        </div>
    </div>
</div>
@stop
