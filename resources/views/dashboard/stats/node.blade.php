@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> 操作日志
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline pull-right">
                        <div class="form-group">
                            <input type="text" name="query[type]" class="form-control" value="" placeholder="类型">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <div class="row">
                    <div class="col-sm-12 striped-list" id="item-list" data-item-name="node">
                        @forelse($nodes as $node)
                            <div class="row striped-list-item" data-item-id="{{ $node->id }}">
                                <div class="col-xs-1">
                                    <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                                </div>
                                <div class="col-xs-2 drag-handle">
                                    <img src="{{ $node->icon }}" alt="" style="max-width: 200px; max-height: 50px;">
                                </div>
                                <div class="col-xs-2 drag-handle">
                                    {!! $node->name.'<br>'.$node->slug !!}
                                </div>
                                <div class="col-xs-2 drag-handle">
                                    {{ $node->section->name }}
                                </div>
                                <div class="col-xs-3 drag-handle">
                                    {{ $node->description }}
                                </div>
                                <div class="col-xs-2 text-right">
                                    <a href="{{ route('dashboard.stat.node_detail',['id'=>$node->id]) }}" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item"><a href="{{ route('dashboard.node.create') }}">{{ trans('dashboard.nodes.add.message') }}</a></div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
