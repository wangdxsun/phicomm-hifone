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
                            <input type="text" name="reply[body]" class="form-control" placeholder="回复内容"
                                   @if (isset($search['body']))
                                   value="{{ $search['body'] }}"
                                    @endif >
                            <input type="text" name="reply[thread_title]" class="form-control" placeholder="帖子标题"
                                   @if (isset($search['thread_title']))
                                   value="{{ $search['thread_title'] }}"
                                    @endif >
                            <input type="text" name="reply[username]" class="form-control" placeholder="回帖人"
                                   @if (isset($search['username']))
                                   value="{{ $search['username'] }}"
                                    @endif >
                        </div>
                        <input name="reply[date_start]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="开始时间"
                               @if (isset($search['date_start']))
                               value="{{ $search['date_start'] }}"
                                @endif >
                        <input name="reply[date_end]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="结束时间"
                               @if (isset($search['date_end']))
                               value="{{ $search['date_end'] }}"
                                @endif >
                        <select class="form-control " name="reply[orderByThreadId]">
                            <option value="" selected>排列方式</option>
                            @foreach ($orderByThreadId as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <select class="form-control " name="reply[orderType]">
                            <option value="" selected>排列方式</option>
                            @foreach ($orderTypes as $key => $orderType)
                                <option value="{{ $key }}">{{ $orderType }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td style="width: 60px;">#</td>
                        <td style="width: 250px;">回帖内容</td>
                        <td style="width: 250px">帖子标题</td>
                        <td style="width: 70px;">版块</td>
                        <td style="width: 100px">回帖人</td>
                        <td style="width: 90px">回帖时间</td>
                        <td style="width: 100px">IP地址</td>
                        <td style="width: 100px">操作人</td>
                        <td style="width: 90px">操作时间</td>
                        <td style="width: 70px">操作</td>
                    </tr>
                    @foreach($replies as $reply)
                        <tr>
                            <td>{{ $reply->id }}</td>
                            <td>
                                <div class="replyContent" >
                                    {!! $reply->body!!}
                                </div>
                                @if(Str::length($reply->body) > 26 || Str::contains($reply->body,['<img']))
                                    <a data-toggle="collapse" href="#thread{{ $reply->id }}" aria-expanded="false">查看更多</a>
                                    <div class="collapse replyImg" id="thread{{ $reply->id }}">{!! $reply->body !!}</div>
                                @endif
                            </td>
                            <td><a href="{{ $reply->thread->url }}" target="_blank" >{{ $reply->thread->title }}</a></td>
                            <td><a href="{{ $reply->thread->node->url }}" target="_blank" >{{ $reply->thread->node->name }}</a></td>
                            <td>
                                @if(!isset($reply->user))
                                    {{ $reply->user }}
                                @else
                                <a href="{{ $reply->user->url }}" target="_blank">{{ $reply->user->username }}</a>
                                @endif
                            </td>
                            <td>{{ $reply->created_at }}</td>
                            <td>{{ $reply->ip }}</td>
                            <td>{{ $reply->lastOpUser->username }}</td>
                            <td>{{ $reply->last_op_time }}</td>
                            <td>
                                <a href="/dashboard/reply/{{ $reply->id }}/edit" title="编辑"><i class="fa fa-pencil"></i></a>
                                <a data-url="/dashboard/reply/{{$reply->id}}/pin" data-method="post" title="置顶"><i class="{{ $reply->pin }}"></i></a>
                                <a data-url="/dashboard/reply/{{ $reply->id }}/index/to/trash" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <!-- Pager -->
                    {!! $replies->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
