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
                                <input type="text" name="comment[body]" class="form-control" placeholder="回复内容" style="width: 100px;"
                                       @if (isset($search['body']))
                                       value="{{ $search['body'] }}"
                                        @endif >
                                <input type="text" name="comment[title]" class="form-control" placeholder="问题标题" style="width: 160px;"
                                       @if (isset($search['title']))
                                       value="{{ $search['title'] }}"
                                        @endif >
                                <input type="text" name="comment[user_id]" class="form-control" placeholder="回复人" style="width: 100px;"
                                       @if (isset($search['user_id']))
                                       value="{{ $search['user_id'] }}"
                                        @endif >
                                {{--按标签筛选，提供所有的问题标签供选择--}}
                                <el-select v-model="questionTags" placeholder="问题类型">
                                    <el-option-group
                                            v-for="questionTagType in questionTagTypes"
                                            :key="questionTagType.id"
                                            :label="questionTagType.display_name">
                                        <el-option
                                                v-for="tag in questionTagType.tags"
                                                :key="tag.id"
                                                :label="tag.name"
                                                :value="tag.id">
                                        </el-option>
                                    </el-option-group>
                                </el-select>
                                <input type="hidden" class="form-control" :value="questionTags" name="comment[tag]">
                            </div>

                            <el-date-picker type="datetime" placeholder="开始时间" v-model="date_start"></el-date-picker>
                            <el-date-picker type="datetime" placeholder="结束时间" v-model="date_end"></el-date-picker>

                            <button class="btn btn-default">搜索</button>
                            <el-input :value="date_end_str" placeholder="请输入内容"  type="hidden" resize="both"  style="width: 60px; height: 10px;" name="question[date_end]"></el-input>
                            <el-input :value="date_start_str" placeholder="请输入内容"  type="hidden" resize="both"  style="width: 60px; height: 10px;" name="question[date_start]"></el-input>
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
                                    <td>
                                        <a href="/dashboard/comment/{{ $comment->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                                        <a data-url="/dashboard/comments/{{ $comment->id }}/index/to/trash" data-title="回复移入回收站" data-method="post" class="need-reason" title="删除"><i class="fa fa-trash"></i></a>
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
                questionTagTypes: {!! $questionTagTypes !!},
                questionTags: '',
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
