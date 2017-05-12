@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                <i class="fa fa-file-text-o"></i> {{ $sub_header }}
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
                    <select class="form-control selectpicker" name="thread[id]">
                        <option value="" selected>全部帖子ID</option>
                        @foreach ($thread_all as $thread)
                            <option value="{{ $thread->id }}">{{ $thread->id }}</option>
                        @endforeach
                    </select>
                    <select class="form-control selectpicker" name="thread[title]" style="max-width: 300px">
                        <option value="" selected>全部帖子标题</option>
                        @foreach ($thread_all as $thread)
                            <option value="{{ $thread->title }}">{{ $thread->title }}</option>
                        @endforeach
                    </select>
                    <select class="form-control selectpicker" name="thread[node_id]">
                        <option value="" selected>全部节点</option>
                        @foreach ($sections as $section)
                            <optgroup label="{{ $section->name }}">
                                @if(isset($section->nodes))
                                    @foreach ($section->nodes as $node)
                                        <option value="{{ $node->id }}">{{ $node->name }}</option>
                                    @endforeach
                                @endif
                            </optgroup>
                        @endforeach
                    </select>
                    <select class="form-control selectpicker" name="thread[user_id]">
                        <option value="" selected>全部发帖人</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                    <input name="thread[date_start]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="开始时间">
                    <input name="thread[date_end]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="结束时间">
                    <button class="btn btn-default">搜索</button>
                </form>
            </div>
            <table class="table table-bordered table-striped table-condensed">
            <tbody>
                <tr class="head">
                    <td class="first">#</td>
                    <td>标题</td>
                    <td>节点</td>
                    <td>发帖人</td>
                    <td>回帖</td>
                    <td>查看</td>
                    <td style="width: 150px">发帖时间</td>
                    <td>操作人</td>
                    <td style="width: 150px">操作时间</td>
                    <td style="width: 110px">操作</td>
                </tr>
                @foreach($threads as $thread)
                <tr>
                    <td>{{ $thread->id }}</td>
                    <td><a target="_blank" href="{{ $thread->url }}">{{ $thread->title }}</a></td>
                    <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                    <td><a href="{{ $thread->user->url }}" target="_blank">{{ $thread->user->username }}</a></td>
                    <td>{{ $thread->reply_count }}</td>
                    <td>{{ $thread->view_count }}</td>
                    <td>{{ $thread->created_time }}</td>
                    <td>{{ $thread->lastOpUser->username }}</td>
                    <td>{{ $thread->last_op_time }}</td>
                    <td>
                        <a data-url="/dashboard/thread/{{$thread->id}}/excellent" data-method="post" title="精华"><i class="{{ $thread->excellent }}"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/pin" data-method="post" title="置顶"><i class="{{ $thread->pin }}"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/sink" data-method="post" title="下沉"><i class="{{ $thread->sink }}"></i></a>
                        <a href="/dashboard/thread/{{ $thread->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                        <a data-url="/dashboard/thread/{{ $thread->id }}/trash" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>

            <div class="text-right">
            <!-- Pager -->
            {!! $threads->appends(Request::except('page', '_pjax'))->render() !!}
    </div>
@stop
