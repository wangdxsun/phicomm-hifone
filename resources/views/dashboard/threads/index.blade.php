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
                    <div class="form-group">
                        <input type="text" name="q" class="form-control" value="" placeholder="帖子标题">
                    </div>
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
                    <td style="width: 150px">时间</td>
                    <td style="width: 85px">操作</td>
                </tr>
                @foreach($threads as $thread)
                <tr>
                    <td>{{ $thread->id }}</td>
                    <td><a target="_blank" href="{{ $thread->url }}">{{ Str::substr($thread->title, 0, 20) }}</a></td>
                    <td>{{ $thread->node->name }}</td>
                    <td><a data-name="{{ $thread->user->username }}" href="{{ $thread->author_url }}">{{ $thread->user->username }}</a></td>
                    <td>{{ $thread->reply_count }}</td>
                    <td>{{ $thread->created_at }}</td>
                    <td>
                        <a data-url="/dashboard/thread/{{$thread->id}}/pin" data-method="post" class="confirm-action"><i class="{{ $thread->pin }}"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/excellent" data-method="post" class="confirm-action"><i class="{{ $thread->excellent }}"></i></a>
                        <a href="/dashboard/thread/{{ $thread->id }}/edit"><i class="fa fa-pencil"></i></a>
                        <a data-url="/dashboard/thread/{{ $thread->id }}/trash" data-method="post" class="confirm-action"><i class="fa fa-trash"></i></a>
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
