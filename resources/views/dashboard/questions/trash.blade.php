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
                截止当前, 列表总数：{{ $questionsCount }}
            </span>
        </div>
        <div class="row">
            <div class="toolbar">
                <form class="form-inline">
                    <div class="form-group">
                        <input type="text" name="question[id]" class="form-control" placeholder="问题编号" style="width: 100px;"
                               @if (isset($search['id']))
                               value="{{ $search['id'] }}"
                                @endif >
                        <input type="text" name="question[title]" class="form-control" placeholder="问题标题" style="width: 160px;"
                               @if (isset($search['title']))
                               value="{{ $search['title'] }}"
                                @endif >
                        <input type="text" name="question[user_id]" class="form-control" placeholder="提问者" style="width: 100px;"
                               @if (isset($search['user_id']))
                               value="{{ $search['user_id'] }}"
                                @endif >
                    </div>
                    <button class="btn btn-default">搜索</button>
                </form>
            </div>
            <div class="col-sm-12">
                    {!! csrf_field() !!}
                    <table class="table table-bordered table-striped table-condensed">
                        <tr class="head">
                            <td style="width: 50px;">编号</td>
                            <td style="width: 180px;">问题标题</td>
                            <td style="width: 180px;">问题类型</td>
                            <td style="width: 70px;">提问者</td>
                            <td style="width: 100px;">IP地址</td>
                            <td style="width: 80px;">提问时间</td>
                            <td style="width: 80px;">悬赏分值</td>
                            <td style="width: 80px;">操作人</td>
                            <td style="width: 80px;">操作时间</td>
                            <td style="width: 80px;">操作原因</td>
                            <td style="width: 70px;">操作</td>
                        </tr>
                        @foreach($questions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->title }}</td>
                                <td>
                                    @foreach($question->tags as $tag)
                                        {{$tag->name}}<br>
                                    @endforeach
                                </td>
                                <td><a href="{{ route('user.show', ['id'=>$question->user->id]) }}" target="_blank">{{ $question->user->username }}</a></td>
                                <td>{{ $question->ip }}</td>
                                <td>{{ $question->created_time }}</td>
                                <td>{{ $question->score }}</td>
                                <td>{{ $question->lastOpUser->username }}</td>
                                <td>{{ $question->last_op_time }}</td>
                                <td>{{ $question->last_op_reason }}</td>
                                <td>
                                    <a data-url="/dashboard/questions/{{$question->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
            </div>
        </div>
    </div>
@stop
