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
                截止当前,列表的回复总数：{{ $commentsCount }}
            </span>
        </div>
            <div class="row" id="app">
                <div class="col-sm-12">
                    <form class="form-inline" method="post" action="/dashboard/comments/batchAudit">
                        {!! csrf_field() !!}
                        <table class="table table-bordered table-striped table-condensed">
                            <button class="btn btn-default">批量审核通过</button>
                            <tbody>
                            <tr class="head">
                                <td style="width: 30px;"><input id="selectAll" type="checkbox"></td>
                                <td style="width: 70px;">编号</td>
                                <td style="width: 180px;">回复内容</td>
                                <td style="width: 180px;">敏感词</td>
                                <td style="width: 80px;">问题标题</td>
                                <td style="width: 90px;">问题类型</td>
                                <td style="width: 50px;">回复人</td>
                                <td style="width: 50px;">IP地址</td>
                                <td style="width: 90px;">回复时间</td>
                                <td style="width: 120px;">操作</td>
                            </tr>
                            @foreach($comments as $comment)
                                <tr>
                                    <td><input class="checkAll" type="checkbox" name="batch[]" value="{{ $comment->id }}"></td>
                                    <td>{{ $comment->id }}</td>
                                    <td>{{ $comment->body }}</td>
                                    <td>{{ $comment->bad_word }}</td>
                                    <td>{{ $comment->answer->question->title }}</td>
                                    <td></td>
                                    <td>
                                        <a href="{{ route('user.show', ['id'=>$comment->user->id]) }}" target="_blank">{{ $comment->user->username }}</a>
                                    </td>
                                    <td>{{ $comment->ip }}</td>
                                    <td>{{ $comment->created_time }}</td>
                                    <td>
                                        <a data-url="/dashboard/comments/{{ $comment->id }}/audit" data-method="post" title="审核通过"><i class="fa fa-check"></i></a>
                                        <a href="/dashboard/comments/{{ $comment->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                                        <a data-url="/dashboard/comments/{{ $comment->id }}/index/to/trash" data-title="问题移入回收站" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
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
