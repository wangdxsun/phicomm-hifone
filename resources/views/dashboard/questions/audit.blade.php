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
                截止当前列表的问题总数：{{ $questionsCount }}
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <form class="form-inline" method="post" action="/dashboard/questions/batchAudit">
                    {!! csrf_field() !!}
                    <table class="table table-bordered table-striped table-condensed">
                        <button class="btn btn-default">批量审核通过</button>
                        <tr class="head">
                            <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                            <td style="width: 50px;">编号</td>
                            <td style="width: 180px;">问题标题</td>
                            <td style="width: 250px;">问题内容</td>
                            <td style="width: 70px;">敏感词</td>
                            <td style="width: 70px;">问题子类</td>
                            <td style="width: 70px;">提问者</td>
                            <td style="width: 80px;">提问时间</td>
                            <td style="width: 80px;">悬赏</td>
                            <td style="width: 70px;">操作</td>
                        </tr>
                        @foreach($questions as $question)
                            <tr>
                                <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $question->id }}"></td>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->title }}</td>
                                <td>
                                    <div class="replyContent">
                                        {!! $question->body !!}
                                    </div>
                                    @if(Str::length($question->body) > 26 || Str::contains($question->body,['<img']))
                                        <a  data-toggle="collapse" href="#question{{ $question->id }}" aria-expanded="false">查看更多</a>
                                        <div  class="collapse well" id="question{{ $question->id }}">{!! $question->body !!}</div>
                                    @endif
                                </td>
                                <td>{{ $question->bad_word }}</td>
                                <td></td>
                                <td><a href="{{ route('user.show', ['id'=>$question->user->id]) }}" target="_blank">{{ $question->user->username }}</a></td>
                                <td>{{ $question->created_time }}</td>
                                <td>{{ $question->score }}</td>
                                <td>
                                    <a data-url="/dashboard/questions/{{$question->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                                    <a href="/dashboard/questions/{{$question->id}}/edit"><i class="fa fa-pencil"></i></a>
                                    <a data-url="/dashboard/questions/{{ $question->id }}/audit/to/trash" data-method="post" class="need-reason"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            </div>
        </div>
    </div>
@stop
