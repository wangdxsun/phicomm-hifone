@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-tag"></i> 问题分类
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.tag.type.create', ['question']) }}">新增问题分类</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12 striped-list" id="item-list" data-item-name="tagType">

                <div class="col-xs-1"><span class="drag-handle">排序</span></div>
                <div class="col-xs-3 drag-handle">问题分类id</div>
                <div class="col-xs-3 drag-handle">分类名称</div>
                <div class="col-xs-3 drag-handle">已有子类</div>
                <div class="col-xs-2 drag-handle">操作</div>

                @foreach($tagTypes as $tagType)
                    <div class="row striped-list-item" data-item-id="{{ $tagType->id }}">
                        <div class="col-xs-1">
                            <span class="drag-handle"><i class="fa fa-navicon"></i></span>
                        </div>
                        <div class="col-xs-3 drag-handle">
                            <span>{{ $tagType->id }}</span>
                        </div>
                        <div class="col-xs-3 drag-handle">
                            {{ $tagType->display_name }}
                        </div>
                        <div class="col-xs-3 drag-handle">
                            @foreach($tagType->tags as $tag)
                                {{ $tag->name . '， ' }}
                            @endforeach
                        </div>
                        <div class="col-xs-2 drag-handle">
                            <a href="/dashboard/question/tag/type/{{ $tagType->id }}/edit/question" title="编辑"><i class="fa fa-pencil"></i></a>
                            <a data-url="{{ route('dashboard.question.tag.type.destroy',['id'=>$tagType->id, 'question']) }}" data-method="delete" class="confirm-action" data-title="是否删除该分类？"><i class="fa fa-trash"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@stop