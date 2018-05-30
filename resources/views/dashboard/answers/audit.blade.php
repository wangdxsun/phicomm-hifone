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
                截止当前, 列表总数：{{ $answersCount }}
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <form class="form-inline" method="post" action="/dashboard/answers/batchAudit">
                    {!! csrf_field() !!}
                    <table class="table table-bordered table-striped table-condensed">
                        <button class="btn btn-default">批量审核通过</button>
                        <tr class="head">
                            <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                            <td style="width: 50px;">编号</td>
                            <td style="width: 180px;">回答内容</td>
                            <td style="width: 70px;">敏感词</td>
                            <td style="width: 70px;">问题标题</td>
                            <td style="width: 70px;">问题类型</td>
                            <td style="width: 70px;">回答人</td>
                            <td style="width: 70px;">IP地址</td>
                            <td style="width: 80px;">回答时间</td>
                            <td style="width: 80px;">悬赏分值</td>
                            <td style="width: 70px;">操作</td>
                        </tr>
                        @foreach($answers as $answer)
                            <tr>
                                <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $answer->id }}"></td>
                                <td>{{ $answer->id }}</td>
                                <td>
                                    <div class="replyContent">
                                        {!! $answer->body !!}
                                    </div>
                                    @if(Str::length($answer->body) > 26 || Str::contains($answer->body,['<img']))
                                        <a  data-toggle="collapse" href="#answer{{ $answer->id }}" aria-expanded="false">查看更多</a>
                                        <div  class="collapse well" id="answer{{ $answer->id }}">{!! $answer->body !!}</div>
                                    @endif
                                </td>
                                <td>{{ $answer->bad_word }}</td>
                                <td><a target="_blank" href="{{ $answer->question->url }}">{{ $answer->question->title }}</a></td>
                                <td>
                                    @foreach($answer->question->tags as $tag)
                                        {{$tag->name}}<br>
                                    @endforeach
                                </td>
                                <td><a href="{{ route('user.show', ['id'=>$answer->user->id]) }}" target="_blank">{{ $answer->user->username }}</a></td>
                                <td>{{ $answer->ip }}</td>
                                <td>{{ $answer->created_at }}</td>
                                <td>{{ $answer->question->score }}</td>
                                <td>
                                    <a data-url="/dashboard/answers/{{$answer->id}}/audit" data-method="post"><i class="fa fa-check"></i></a>
                                    <a href="/dashboard/answer/{{$answer->id}}/edit"><i class="fa fa-pencil"></i></a>
                                    <a data-url="/dashboard/answers/{{ $answer->id }}/audit/to/trash" data-method="post" class="need-reason"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            </div>
        </div>
    </div>
@stop
