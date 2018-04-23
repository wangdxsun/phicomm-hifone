@extends('layouts.dashboard')

@section('content')
<div class="content-wrapper">
    <div class="header sub-header">
        <span class="uppercase">
            <i class="fa fa-file-text-o"></i> {{ trans('dashboard.content.content') }}
        </span>
        <div class="clearfix"></div>
    </div>
    <div class="uppercase pull-right">
            <span class="uppercase">
                截止当前列表的回帖总数：{{ $replyCount }}
            </span>
    </div>
@if(isset($sub_nav))
@include('dashboard.partials.sub-nav')
@endif
<div class="row">
    <div class="col-sm-12">
        <form class="form-inline" method="post" action="/dashboard/reply/batchAudit">
        {!! csrf_field() !!}

        <table class="table table-bordered table-striped table-condensed">
            <button class="btn btn-default">
                {{trans('dashboard.replies.batch.audit')}}</button>
        <tbody>
            <tr class="head">
                <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                <td style="width: 60px">#</td>
                <td style="width: 250px;">回帖内容</td>
                <td style="width: 80px">敏感词</td>
                <td style="width: 250px">帖子标题</td>
                <td style="width: 250px">设备信息</td>
                <td style="width: 80px;">回帖人</td>
                <td style="width: 110px">IP地址</td>
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
                    @if(Str::length($reply->body) > 26 || Str::contains($reply->body,['<img']))
                        <a data-toggle="collapse" href="#thread{{ $reply->id }}" aria-expanded="false">查看更多</a>
                        <div class="collapse well" id="thread{{ $reply->id }}">{!! $reply->body !!}</div>
                    @endif
                </td>
                <td>{{ $reply->bad_word }}</td>
                <td><a href="{{ $reply->thread->url }}" target="_blank" >{{ $reply->thread->title }}</a></td>
                <td>
                    @if(sizeof($reply->dev_info) > 0)
                        <a data-toggle="collapse" href="#dev_info{{ $reply->id }}" aria-expanded="false">查看更多</a>
                        <div class="collapse well" id="dev_info{{ $reply->id }}" style="min-width: 230px">
                            @foreach($reply->dev_info as $info)
                                @foreach($info as $key => $item)
                                    {{$key." : ".$item}}<br>
                                @endforeach
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    @if(!isset($reply->user))
                        {{ $reply->user }}
                    @else
                        <a href="{{ route('user.show', ['id'=>$reply->user->id]) }}" target="_blank">{{  $reply->user->username  }}</a>
                    @endif
                </td>
                <td>{{ $reply->ip }}</td>
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
