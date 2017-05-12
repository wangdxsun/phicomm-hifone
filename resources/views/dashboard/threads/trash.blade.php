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
                        <select class="form-control selectpicker" name="thread[id]" style="max-width: 300px">
                            <option value="" selected>全部帖子ID</option>
                            @foreach ($threadAll as $thread)
                                <option value="{{ $thread->id }}">{{ $thread->id }}</option>
                            @endforeach
                        </select>
                        <select class="form-control selectpicker" name="thread[title]" style="max-width: 300px">
                            <option value="" selected>全部帖子标题</option>
                            @foreach ($threadAll as $thread)
                                <option value="{{ $thread->title }}">{{ $thread->title }}</option>
                            @endforeach
                        </select>
                        <select class="form-control selectpicker" name="thread[node_id]">
                            <option value="" selected>全部板块</option>
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
                        <select class="form-control selectpicker" name="thread[last_op_user_id]">
                            <option value="" selected>全部操作人</option>
                            @foreach ($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->username }}</option>
                            @endforeach
                        </select>
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
                        <td>发帖时间</td>
                        <td>操作人</td>
                        <td>操作时间</td>
                        <td>操作原因</td>
                        <td>操作</td>
                    </tr>
                    @foreach($threads as $thread)
                        <tr>
                            <td>{{ $thread->id }}</td>
                            <td><a href="{{ $thread->url }}" target="_blank" ><i class="{{ $thread->icon }}"></i> {{ $thread->title }}</a></td>
                            <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                            <td><a href="{{ $thread->user->url }}" target="_blank">{{ $thread->user->username }}</a></td>
                            <td>{{ $thread->created_time }}</td>
                            <td>{{ $thread->lastOpUser->username }}</td>
                            <td>{{ $thread->last_op_time }}</td>
                            <td>{{ $thread->last_op_reason }}</td>
                            <td>
                                <a data-url="/dashboard/thread/{{$thread->id}}/recycle" data-method="post" class="confirm-action"><i class="fa fa-check"></i></a>
                                <a data-url="/dashboard/thread/{{ $thread->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="text-right">
                    <!-- Pager -->
                    {!! $threads->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
