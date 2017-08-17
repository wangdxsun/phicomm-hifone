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
                        <input type="text" name="thread[id]" class="form-control" placeholder="帖子ID"
                               @if (isset($search['id']))
                                  value="{{ $search['id'] }}"
                               @endif >
                        <input type="text" name="thread[title]" class="form-control" placeholder="帖子标题"
                               @if (isset($search['title']))
                                    value="{{ $search['title'] }}"
                               @endif >
                        <input type="text" name="thread[user_id]" class="form-control" placeholder="发帖人"
                               @if (isset($search['user_id']))
                                    value="{{ $search['user_id'] }}"
                               @endif >
                    </div>
                    <select class="form-control" name="thread[node_id]">
                        <option value = "" selected>全部节点</option>
                        @foreach ($sections as $section)
                            <optgroup label="{{ $section->name }}">
                                @if(isset($section->nodes))
                                    @foreach ($section->nodes as $node)
                                        <option value="{{ $node->id }}" >{{ $node->name }}</option>
                                    @endforeach
                                @endif
                            </optgroup>
                        @endforeach
                    </select>
                    <input name="thread[date_start]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="开始时间"
                           @if (isset($search['date_start']))
                           value="{{ $search['date_start'] }}"
                           @endif >
                    <input name="thread[date_end]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="结束时间"
                           @if (isset($search['date_end']))
                           value="{{ $search['date_end'] }}"
                           @endif >

                    <select class="form-control " name="thread[orderType]">
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
                    <td style="width: 70px;">#</td>
                    <td>标题</td>
                    <td style="width: 80px;">版块</td>
                    <td style="width: 120px;">发帖人</td>
                    <td style="width: 100px;">IP地址</td>
                    <td style="width: 60px;">热度值</td>
                    <td style="width: 50px;">回帖</td>
                    <td style="width: 50px;">查看</td>
                    <td style="width: 150px;">发帖时间</td>
                    <td style="width: 100px;">操作人</td>
                    <td style="width: 150px;">操作时间</td>
                    <td style="width: 120px;">操作</td>
                </tr>
                @foreach($threads as $thread)
                <tr>
                    <td>{{ $thread->id }}</td>
                    <td><a target="_blank" href="{{ $thread->url }}">{{ $thread->title }}</a></td>
                    <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                    <td><a href="{{ $thread->user->url }}" target="_blank">{{ $thread->user->username }}</a></td>
                    <td>{{ $thread->ip }}</td>
                    <td>{{ $thread->heat }}</td>
                    <td>{{ $thread->reply_count }}</td>
                    <td>{{ $thread->view_count }}</td>
                    <td>{{ $thread->created_time }}</td>
                    <td>{{ $thread->lastOpUser->username }}</td>
                    <td>{{ $thread->last_op_time }}</td>
                    <td>
                        <a data-url="/dashboard/thread/{{$thread->id}}/excellent" data-method="post" title="精华"><i class="{{ $thread->excellent }}"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/pin" data-method="post" title="置顶"><i class="{{ $thread->pin }}"></i></a>
                        <a data-url="/dashboard/thread/{{ $thread->id }}/heat_offset" get-url="/dashboard/thread/{{ $thread->id }}/heat_offset" data-method="post" class="getAndSet" title="提升"><i class="fa fa-level-up"></i></a>
                        <a data-url="/dashboard/thread/{{$thread->id}}/sink" data-method="post" title="下沉"><i class="{{ $thread->sink }}"></i></a>
                        <a href="/dashboard/thread/{{ $thread->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                        <a data-url="/dashboard/thread/{{ $thread->id }}/index/to/trash" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
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
