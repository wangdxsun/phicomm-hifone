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
                        <td style="width: 30px;">#</td>
                        <td style="width: 250px;">标题</td>
                        <td >帖子内容</td>
                        <td style="width: 80px;">节点</td>
                        <td style="width: 50px;">发帖人</td>
                        <td style="width: 40px;">回帖</td>
                        <td style="width: 150px;">时间</td>
                        <td style="width: 50px;">操作</td>
                    </tr>
                    @foreach($threads as $thread)
                        <tr>
                            <td>{{ $thread->id }}</td>
                            <td><a target="_blank" href="{{ $thread->url }}"><i class="{{ $thread->icon }}"></i> {{ Str::substr($thread->title, 0, 20) }}</a></td>
                            <td>
                                <a data-toggle="collapse" href="#thread{{ $thread->id }}" aria-expanded="false">{{ Str::substr($thread->body, 0, 100) }}</a>
                                <div class="collapse well" id="thread{{ $thread->id }}">{!! $thread->body !!}</div>
                            </td>
                            <td>{{ $thread->node->name }}</td>
                            <td><a data-name="{{ $thread->user->username }}" href="{{ $thread->author_url }}">{{ $thread->user->username }}</a></td>
                            <td>{{ $thread->reply_count }}</td>
                            <td>{{ $thread->created_at }}</td>
                            <td>
                                <a data-url="/dashboard/thread/{{$thread->id}}/audit" data-method="post" class="confirm-action"><i class="fa fa-check"></i></a>
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
            </div>
        </div>
    </div>
@stop
