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
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td style="width: 30px;">#</td>
                        <td style="width: 250px;">标题</td>
                        <td >帖子内容</td>
                        <td style="width: 70px;">节点</td>
                        <td style="width: 50px;">发帖人</td>
                        <td style="width: 90px;">时间</td>
                        <td style="width: 70px;">操作</td>
                    </tr>
                    @foreach($threads as $thread)
                        <tr>
                            <td>{{ $thread->id }}</td>
                            <td><a target="_blank" href="{{ $thread->url }}"><i class="{{ $thread->icon }}"></i> {{ $thread->title }}</a></td>
                            <td>
                                {{ Str::substr($thread->body, 0, 100) }}
                                @if(Str::length($thread->body) > 100)
                                    <a data-toggle="collapse" href="#thread{{ $thread->id }}" aria-expanded="false">查看更多</a>
                                    <div class="collapse well" id="thread{{ $thread->id }}">{!! $thread->body !!}</div>
                                @endif
                            </td>
                            <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                            <td><a href="{{ $thread->user->url }}" target="_blank">{{ $thread->user->username }}</a></td>
                            <td>{{ $thread->created_time }}</td>
                            <td>
                                <a data-url="/dashboard/thread/{{$thread->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                                <a href="/dashboard/thread/{{$thread->id}}/edit"><i class="fa fa-pencil"></i></a>
                                <a data-url="/dashboard/thread/{{ $thread->id }}/trash" data-method="post" class="need-reason"><i class="fa fa-trash"></i></a>
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
