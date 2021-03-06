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
                <div class="toolbar">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="reply[thread_title]" class="form-control" placeholder="帖子标题"
                                   @if (isset($search['thread_title']))
                                   value="{{ $search['thread_title'] }}"
                                    @endif >
                            <input type="text" name="reply[username]" class="form-control" placeholder="回帖人"
                                   @if (isset($search['username']))
                                   value="{{ $search['username'] }}"
                                    @endif >
                        </div>
                        <select class="form-control " name="reply[last_op_user_id]">
                            <option value="" selected>全部操作人</option>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->username }}</option>
                            @endforeach
                        </select>
                        <div class="form-group">
                            <input type="text" name="reply[body]" class="form-control" placeholder="回帖内容">
                        </div>
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
                        <td style="width:60px">#</td>
                        <td style="width: 250px;">回帖内容</td>
                        <td style="width:200px">帖子标题</td>
                        <td style="width: 250px">设备信息</td>
                        <td style="width: 100px">回帖人</td>
                        <td style="width: 110px">IP地址</td>
                        <td style="width: 90px">回帖时间</td>
                        <td style="width: 100px">操作人</td>
                        <td style="width: 100px">操作原因</td>
                        <td style="width: 90px">操作时间</td>
                        <td style="width: 50px">操作</td>
                    </tr>
                    @foreach($replies as $reply)
                        <tr>
                            <td>{{ $reply->id }}</td>
                            <td>
                                <div class="replyContent">
                                    {!! $reply->body !!}
                                </div>
                                @if(Str::length($reply->body) > 26 || Str::contains($reply->body,['<img']))
                                    <a data-toggle="collapse" href="#thread{{ $reply->id }}" aria-expanded="false">查看更多</a>
                                    <div class="collapse well" id="thread{{ $reply->id }}">{!! $reply->body !!}</div>
                                @endif
                            </td>
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
                            <td><a href="{{ route('user.show', ['id'=>$reply->user->id]) }}" target="_blank">{{  $reply->user->username  }}</a></td>
                            <td>{{ $reply->ip }}</td>
                            <td>{{ $reply->created_at }}</td>
                            <td>{{ $reply->lastOpUser->username }}</td>
                            <td>{{ $reply->last_op_reason }}</td>
                            <td>{{ $reply->last_op_time }}</td>
                            <td>
                                <a data-url="/dashboard/reply/{{ $reply->id }}/recycle" data-method="post" class="confirm-action"><i class="fa fa-check"></i></a>
                                {{--<a data-url="/dashboard/reply/{{ $reply->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>--}}
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
