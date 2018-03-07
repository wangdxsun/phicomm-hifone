@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-tag"></i> 标签分类
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.tag.type.create') }}">新增标签分类</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">标签分类id</td>
                        <td>分类名</td>
                        <td>已有标签</td>
                        <td>标签类型</td>
                        <td>操作</td>
                    </tr>
                    @foreach($tagTypes as $tagType)
                        <tr>
                            <td>{{ $tagType->id }}</td>
                            <td>{{ $tagType->display_name }}</td>
                            <td>
                                @foreach($tagType->tags as $tag)
                                    {{ $tag->name . ' ' }}
                                @endforeach
                            </td>
                            <td>
                                {{ $tagType->type == 0 ? '帖子标签' : '用户标签' }}
                            </td>
                            <td>
                                <a href="/dashboard/tag/type/{{ $tagType->id }}/edit" title="编辑"><i class="fa fa-pencil"></i></a>
                                <a href="/dashboard/tag/type/{{ $tagType->id }}/destroy" title="删除"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>

@stop