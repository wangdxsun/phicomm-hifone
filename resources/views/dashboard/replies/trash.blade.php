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
                        <select class="form-control selectpicker" name="reply[thread_id]" style="max-width: 300px">
                            <option value="" selected>全部帖子标题</option>
                            @foreach ($threads as $thread)
                                <option value="{{ $thread->id }}">{{ $thread->title }}</option>
                            @endforeach
                        </select>
                        <select class="form-control selectpicker" name="reply[user_id]">
                            <option value="" selected>全部回帖人</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                        <select class="form-control selectpicker" name="reply[last_op_user_id]">
                            <option value="" selected>全部操作人</option>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->username }}</option>
                            @endforeach
                        </select>
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
                        <td >回帖内容</td>
                        <td style="width:200px">话题</td>
                        <td style="min-width: 100px">回帖人</td>
                        <td style="width: 90px">回帖时间</td>
                        <td>操作人</td>
                        <td style="width: 100px">操作原因</td>
                        <td style="width: 90px">操作时间</td>
                        <td style="width: 50px">操作</td>
                    </tr>
                    @foreach($replies as $reply)
                        <tr>
                            <td>{{ $reply->id }}</td>
                            <td>{!! $reply->body !!}</td>
                            <td><a href="{{ $reply->thread_url }}" target="_blank" >{{ $reply->title }}</a></td>
                            <td><a href="{{ $reply->user->url }}">{{ $reply->user->username }}</a></td>
                            <td>{{ $reply->created_at }}</td>
                            <td>{{ $reply->lastOpUser->username }}</td>
                            <td>{{ $reply->last_op_reason }}</td>
                            <td>{{ $reply->last_op_time }}</td>
                            <td>
                                <a href="/dashboard/reply/{{ $reply->id }}/edit"><i class="fa fa-pencil"></i></a>
                                <a data-url="/dashboard/reply/{{ $reply->id }}" data-method="delete" class="need-reason"><i class="fa fa-trash"></i></a>
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
