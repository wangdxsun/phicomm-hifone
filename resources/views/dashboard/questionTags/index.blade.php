@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        @if(isset($sub_nav))
            @include('dashboard.partials.sub-nav')
        @endif
        <div class="header sub-header">
            <i class="fa fa-tag"></i> 问题子类
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.question.tag.create', ['question']) }}">新增问题子类</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12 striped-list" id="item-list" data-item-name="tag">
                <div class="row striped-list-item">
                    <div class="col-xs-1"><span>排序</span></div>
                    <div class="col-xs-3"><span>编号</span></div>
                    <div class="col-xs-3"><span>子类名</span></div>
                    <div class="col-xs-3"><span>所属分类</span></div>
                    <div class="col-xs-2"><span>操作</span></div>
                </div>
                @foreach($tags as $tag)
                    <div class="row striped-list-item" data-item-id="{{ $tag->id }}">
                        <div class="col-xs-1">
                            <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                        </div>
                        <div class="col-xs-3 drag-handle">
                            <span>{{ $tag->id }}</span>
                        </div>
                        <div class="col-xs-3 drag-handle">
                            {{ $tag->name }}
                        </div>
                        <div class="col-xs-3 drag-handle">
                            {{ $tag->tagType ? $tag->tagType->display_name : '自动标签'}}
                        </div>
                        <div class="col-xs-2 drag-handle">
                            <a href="/dashboard/question/tag/{{ $tag->id }}/edit/question" title="编辑"><i class="fa fa-pencil"></i></a>
                            <a href="/dashboard/question/tag/{{ $tag->id }}/destroy/question" title="删除"><i class="fa fa-trash"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@stop