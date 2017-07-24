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
        <form class="form-inline" method="post" action="/dashboard/reply/batchAudit">
        {!! csrf_field() !!}
        @include('partials.errors')
        <table class="table table-bordered table-striped table-condensed">
            <button class="btn btn-default">
                {{trans('dashboard.replies.batch.audit')}}</button>
        <tbody>
            <tr class="head">
                <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                <td style="width: 80px">#</td>
                <td >回帖内容</td>
                <td style="width: 250px">帖子标题</td>
                <td style="width: 80px;">回帖人</td>
                <td style="width: 90px">回帖时间</td>
                <td style="width: 80px">操作</td>
            </tr>
            @foreach($replies as $reply)
            <tr>
                <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $reply->id }}"></td>
                <td id="taId">{{ $reply->id }}</td>
                <td>
                    <div class="replyContent" >
                        {!! $reply->body !!}
                    </div>
                    @if(Str::length($reply->body) > 50 || Str::contains($reply->body,['<img']))
                        <a data-toggle="collapse" href="#thread{{ $reply->id }}" aria-expanded="false">查看更多</a>
                        <div class="collapse well" id="thread{{ $reply->id }}">{!! $reply->body !!}</div>
                    @endif
                </td>
                <td><a href="{{ $reply->thread->url }}" target="_blank" >{{ $reply->thread->title }}</a></td>
                <td><a data-name="{{ $reply->user->username }}" href="{{ $reply->user->url }}">{{ $reply->user->username }}</a></td>
                <td>{{ $reply->created_at }}</td>
                <td>
                    <a data-url="/dashboard/reply/{{$reply->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                    <a href="/dashboard/reply/{{ $reply->id }}/edit"><i class="fa fa-pencil"></i></a>
                    <a data-url="/dashboard/reply/{{ $reply->id }}/audit/to/trash" data-method="post" class="need-reason"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
        </form>
         <div class="text-right">
        <!-- Pager -->
        {!! $replies->appends(Request::except('page', '_pjax'))->render() !!}
         </div>
    </div>
</div>
</div>
@stop
