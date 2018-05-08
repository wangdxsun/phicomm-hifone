@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        @if(isset($sub_menu))
            @foreach($sub_menu as $key => $item)
                @if ($key == $current_menu && array_get($item, 'sub_nav'))
                    @include('dashboard.partials.sub-nav', ['sub_nav' => $item['sub_nav']])
                @endif
            @endforeach
        @endif
        <div class="uppercase pull-right">
            <span class="uppercase">
                截止当前列表的问题总数：{{ $answersCount }}
            </span>
        </div>
        <div class="row">
            <div class="toolbar">
                <form class="form-inline">
                    <div class="form-group">
                        <input type="text" name="answer[body]" class="form-control" placeholder="回答内容" style="width: 160px;"
                               @if (isset($search['body']))
                               value="{{ $search['body'] }}"
                                @endif >
                        <input type="text" name="answer[title]" class="form-control" placeholder="问题标题" style="width: 160px;"
                               @if (isset($search['title']))
                               value="{{ $search['title'] }}"
                                @endif >
                        <input type="text" name="answer[user_name]" class="form-control" placeholder="回答人" style="width: 100px;"
                               @if (isset($search['user_name']))
                               value="{{ $search['user_name'] }}"
                                @endif >
                    </div>
                    <button class="btn btn-default">搜索</button>
                </form>
            </div>
            <div class="col-sm-12">
                {!! csrf_field() !!}
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td style="width: 70px;">编号</td>
                        <td style="width: 180px;">回答内容</td>
                        <td style="width: 180px;">问题标题</td>
                        <td style="width: 180px;">问题类型</td>
                        <td style="width: 80px;">回答人</td>
                        <td style="width: 90px;">悬赏</td>
                        <td style="width: 90px;">IP地址</td>
                        <td style="width: 90px;">回答时间</td>
                        <td style="width: 80px;">操作人</td>
                        <td style="width: 90px;">操作时间</td>
                        <td style="width: 90px;">操作原因</td>
                        <td style="width: 120px;">操作</td>
                    </tr>
                    @foreach($answers as $answer)
                        <tr>
                            <td>{{ $answer->id }}</td>
                            <td>{{ $answer->body }}</td>
                            <td>{{ $answer->question->title }}</td>
                            <td></td>
                            <td>
                                <a href="{{ route('user.show', ['id'=>$answer->user->id]) }}" target="_blank">{{ $answer->user->username }}</a>
                            </td>
                            <td>{{ $answer->question->score }}</td>
                            <td>{{ $answer->ip }}</td>
                            <td>{{ $answer->created_time }}</td>
                            <td>{{ $answer->lastOpUser->username }}</td>
                            <td>{{ $answer->last_op_time }}</td>
                            <td>{{ $answer->last_op_reason }}</td>
                            <td>
                                <a data-url="/dashboard/answers/{{$answer->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop
