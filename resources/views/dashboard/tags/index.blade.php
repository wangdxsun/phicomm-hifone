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
            <i class="fa fa-tag"></i> 用户标签
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.tag.create', ['user']) }}">新增用户标签</a>
            @endif
        </div>
        <div class="row">
            <td class="col-sm-12">
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">标签编号</td>
                        <td>标签名</td>
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
                                    <a href="/dashboard/tag/{{ $tag->id }}/edit/user" title="编辑"><i class="fa fa-pencil"></i></a>
                                    <a href="/dashboard/tag/{{ $tag->id }}/destroy/user" title="删除"><i class="fa fa-trash"></i></a>
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