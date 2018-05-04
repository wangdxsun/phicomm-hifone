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
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.question.tag.create') }}">新增问题子类</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">子类编号</td>
                        <td>子类名</td>
                        <td>所属分类</td>
                        <td>操作</td>
                    </tr>
                    @foreach($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>{{ $tag->tagType ? $tag->tagType->display_name : '自动标签'}}</td>
                            @if ($tag->tagType  && $tag->tagType->display_name != '自动标签')
                                <td>
                                    <a href="/dashboard/question/tag/{{ $tag->id }}/edit" title="编辑"><i class="fa fa-pencil"></i></a>
                                    <a href="/dashboard/question/tag/{{ $tag->id }}/destroy" title="删除"><i class="fa fa-trash"></i></a>
                                </td>
                            @else
                                <td></td>
                            @endif
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>

@stop