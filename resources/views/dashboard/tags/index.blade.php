@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-tag"></i> 标签
            @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.tag.create') }}">新增标签</a>
            @endif
        </div>
        @if(isset($sub_nav))
            @include('dashboard.partials.sub-nav')
        @endif
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">标签id</td>
                        <td>标签名</td>
                        <td>所属分类</td>
                        <td>操作</td>
                    </tr>
                    @foreach($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>{{ $tag->tagType->display_name}}</td>
                            <td>
                                <a href="/dashboard/tag/{{ $tag->id }}/edit" title="编辑"><i class="fa fa-pencil"></i></a>
                                <a href="/dashboard/tag/{{ $tag->id }}/destroy" title="删除"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>
        </div>
    </div>

@stop