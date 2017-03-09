@extends('layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <div class="header sub-header">
        <span class="uppercase">
            <i class="fa fa-file-text-o"></i> {{ trans('dashboard.content.content') }}
        </span>
        <div class="clearfix"></div>
    </div>
@if(isset($sub_menu))
@include('dashboard.partials.sub-nav')
@endif
<div class="row">
    <div class="col-sm-12">
        @include('partials.errors')
        <div class="toolbar">
            <form class="form-inline">
                <div class="form-group">
                    <input type="text" name="q" class="form-control" value="" placeholder="回帖内容">
                </div>
                <button class="btn btn-default">搜索</button>
            </form>
        </div>
        <table class="table table-bordered table-striped table-condensed">
        <tbody>
            <tr class="head">
                <td class="first">#</td>
                <td style="width: 250px">话题</td>
                <td>回帖内容</td>
                <td style="width: 100px">回帖人</td>
                <td style="width: 150px">回帖时间</td>
                <td style="width: 100px">操作</td>
            </tr>
            @foreach($replies as $reply)
            <tr>
                <td>{{ $reply->id }}</td>
                <td><a target="_blank" href="{{ $reply->url }}">{{ $reply->title }}</a></td>
                {{--<td>{{ Str::words($reply->body_original, 5) }}</td>--}}
                <td>{{ $reply->body_original }}</td>
                <td><a data-name="{{ $reply->user->username }}" href="{{ $reply->author_url }}">{{ $reply->user->username }}</a></td>
                <td>{{ $reply->created_at }}</td>
                <td>
                    <a href="/dashboard/reply/{{ $reply->id }}/edit"><i class="fa fa-pencil"></i></a>
                    <a data-url="/dashboard/reply/{{ $reply->id }}/trash" data-method="post" class="confirm-action"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
         <div class="text-right">
        <!-- Pager -->
        {!! $replies->appends(Request::except('page', '_pjax'))->render() !!}
</div>
@stop
