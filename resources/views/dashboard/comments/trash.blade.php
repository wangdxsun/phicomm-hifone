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
                截止当前,列表总数：{{ $commentsCount }}
            </span>
        </div>
            <div class="row" id="app">
                <div class="col-sm-12">
                    <div class="toolbar">
                        <form class="form-inline">
                            <div class="form-group">
                                <input type="text" name="comment[id]" class="form-control" placeholder="回复ID" style="width: 100px;"
                                       @if (isset($search['id']))
                                       value="{{ $search['id'] }}"
                                        @endif >
                                <input type="text" name="comment[title]" class="form-control" placeholder="问题标题" style="width: 160px;"
                                       @if (isset($search['title']))
                                       value="{{ $search['title'] }}"
                                        @endif >
                                <input type="text" name="comment[user_id]" class="form-control" placeholder="回复人" style="width: 100px;"
                                       @if (isset($search['user_id']))
                                       value="{{ $search['user_id'] }}"
                                        @endif >
                            </div>
                            <button class="btn btn-default">搜索</button>
                        </form>
                    </div>
                    <form method="POST">
                        {!! csrf_field() !!}
                        <table class="table table-bordered table-striped table-condensed">
                            <tbody>
                            <tr class="head">
                                <td style="width: 70px;">编号</td>
                                <td style="width: 180px;">回复内容</td>
                                <td style="width: 80px;">问题标题</td>
                                <td style="width: 90px;">问题类型</td>
                                <td style="width: 50px;">回复人</td>
                                <td style="width: 50px;">IP地址</td>
                                <td style="width: 90px;">回复时间</td>
                                <td style="width: 80px;">操作人</td>
                                <td style="width: 90px;">操作时间</td>
                                <td style="width: 90px;">操作原因</td>
                                <td style="width: 120px;">操作</td>
                            </tr>
                            @foreach($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>
                                        <div class="replyContent">
                                            {!! $comment->body !!}
                                        </div>
                                        @if(Str::length($comment->body) > 26 || Str::contains($comment->body,['<img']))
                                            <a  data-toggle="collapse" href="#comment{{ $comment->id }}" aria-expanded="false">查看更多</a>
                                            <div  class="collapse well" id="comment{{ $comment->id }}">{!! $comment->body !!}</div>
                                        @endif
                                    </td>
                                    <td>{{ $comment->answer->question->title }}</td>
                                    <td>
                                        @foreach($comment->answer->question->tags as $tag)
                                            {{ $tag->name }}<br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('user.show', ['id'=>$comment->user->id]) }}" target="_blank">{{ $comment->user->username }}</a>
                                    </td>
                                    <td>{{ $comment->ip }}</td>
                                    <td>{{ $comment->created_time }}</td>
                                    <td>{{ $comment->lastOpUser->username }}</td>
                                    <td>{{ $comment->last_op_time }}</td>
                                    <td>{{ $comment->last_op_reason }}</td>
                                    <td>
                                        <a data-url="/dashboard/comments/{{ $comment->id }}/audit" data-method="post" title="审核通过"><i class="fa fa-check"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </form>
                </div>
                <div class="text-right">
                </div>
            </div>
    </div>
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                date_start:"",
                date_end:"",
            };
        },
        computed: {
            date_start_str: function () {
                return this.date_start === '' ? '' : this.date_start.format('yyyy-MM-dd hh:mm:ss');
            },
            date_end_str: function () {
                return this.date_end === '' ? '' : this.date_end.format('yyyy-MM-dd hh:mm:ss');
            }
        }
    });
</script>
@stop
