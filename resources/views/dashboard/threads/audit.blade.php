@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                <i class="fa fa-file-text-o"></i> {{ $sub_header }}
            </span>
            <div class="clearfix"></div>
        </div>
        <div class="uppercase pull-right">
            <span class="uppercase">
                截止当前列表的帖子总数：{{ $threadCount }}
            </span>
        </div>
        @if(isset($sub_nav))
            @include('dashboard.partials.sub-nav')
        @endif
        <div class="row">
            <div class="col-sm-12">
                <form class="form-inline" method="post" action="/dashboard/thread/batchAudit">
                {!! csrf_field() !!}
                <table class="table table-bordered table-striped table-condensed">
                    <button class="btn btn-default">
                        {{trans('dashboard.threads.batch.audit')}}</button>
                    <tbody>
                    <tr class="head">
                        <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                        <td style="width: 50px;">#</td>
                        <td style="width: 180px;">标题</td>
                        <td style="width: 250px;">帖子内容</td>
                        <td style="width: 70px;">敏感词</td>
                        <td style="width: 70px;">主版块</td>
                        <td style="width: 70px;">子版块</td>
                        <td style="width: 70px;">发帖人</td>
                        <td style="width: 60px;">来源</td>
                        <td style="width: 90px;">用户设备信息</td>
                        <td style="width: 100px;">IP地址</td>
                        <td style="width: 80px;">发贴时间</td>
                        <td style="width: 70px;">操作</td>
                    </tr>
                    @foreach($threads as $thread)
                        <tr>
                            <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $thread->id }}"></td>
                            <td>{{ $thread->id }}</td>
                            <td><a target="_blank" href="{{ $thread->url }}"><i class="{{ $thread->icon }}"></i> {{ $thread->title }}</a></td>
                            <td>
                                <div class="replyContent">
                                    {!! $thread->body !!}
                                </div>
                                @if(Str::length($thread->body) > 26 || Str::contains($thread->body,['<img']))
                                    <a  data-toggle="collapse" href="#thread{{ $thread->id }}" aria-expanded="false">查看更多</a>
                                    <div  class="collapse well" id="thread{{ $thread->id }}">{!! $thread->body !!}</div>
                                @endif
                            </td>
                            <td>{{ $thread->bad_word }}</td>
                            <td><a href="{{ $thread->node->url }}" target="_blank">{{ $thread->node->name }}</a></td>
                            <td>
                                @if($thread->sub_node_id == 0)
                                    {{ '' }}
                                @else
                                    {{ $thread->subNode->name }}
                                @endif
                                </td>
                            <td>
                                @if(!isset($thread->user))
                                    {{ '' }}
                                @else
                                    <a href="{{ route('user.show', ['id'=>$thread->user->id]) }}" target="_blank">{{  $thread->user->username  }}</a>
                                @endif
                            </td>
                            <td>{{ $thread->channel == 0 ? "社区" : "意见反馈" }}</td>
                            <td>
                                @if(sizeof($thread->dev_info) > 0)
                                    <a data-toggle="collapse" href="#dev_info{{ $thread->id }}" aria-expanded="false">查看更多</a>
                                    <div class="collapse well" id="dev_info{{ $thread->id }}" style="min-width: 230px">
                                        @foreach($thread->dev_info as $info)
                                            @foreach($info as $key => $item)
                                                {{$key." : ".$item}}<br>
                                            @endforeach
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>{{ $thread->ip }}</td>
                            <td>{{ $thread->created_time }}</td>
                            <td>
                                <a data-url="/dashboard/thread/{{$thread->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                                <a href="/dashboard/thread/{{$thread->id}}/edit"><i class="fa fa-pencil"></i></a>
                                <a data-url="/dashboard/thread/{{ $thread->id }}/audit/to/trash" data-method="post" class="need-reason"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </form>
                <div class="text-right">..
                    <!-- Pager -->
                    {!! $threads->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
