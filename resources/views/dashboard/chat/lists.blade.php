@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper" id="app">
        @if(isset($sub_nav))
            @include('dashboard.partials.sub-nav')
        @endif
        <div class="row">
            <div class="col-sm-12">
                <form class="form-inline">
                    <el-input v-model="chat_id" type="text" name="chat[id]" resize="both" style="width: 160px; height: 10px;" placeholder="私信ID"></el-input>
                    <el-input v-model="from_user_id" type="text" name="chat[from_user_id]" resize="both" style="width: 160px; height: 10px;" placeholder="发信人"></el-input>
                    <el-input v-model="to_user_id" type="text" name="chat[to_user_id]" resize="both"  style="width: 160px; height: 10px;" placeholder="收信人"></el-input>
                    <el-input v-model="message" type="text" name="chat[message]" resize="both"  style="width: 160px; height: 10px;" placeholder="私信内容"></el-input>
                    <template>
                        <el-date-picker type="datetime" placeholder="开始时间" v-model="date_start"></el-date-picker>
                        <el-date-picker type="datetime" placeholder="结束时间" v-model="date_end"></el-date-picker>
                        <el-input :value="date_start_str" placeholder="请输入内容" type="hidden" resize="both"  style="width: 60px; height: 10px;" name="chat[date_start]"></el-input>
                        <el-input :value="date_end_str" placeholder="请输入内容" type="hidden" resize="both"  style="width: 60px; height: 10px;" name="chat[date_end]"></el-input>
                    </template>
                    <button class="btn btn-default">搜索</button>
                </form>
                <table  class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td style="width: 80px;">编号</td>
                        <td style="width: 100px;">发信者</td>
                        <td style="width: 100px;">收信者</td>
                        <td>私信内容</td>
                        <td style="width: 160px;">发送日期</td>
                    </tr>
                    @foreach($chats as $chat)
                        <tr>
                            <td>{{ $chat->id }}</td>
                            <td>{{ $chat->from->username }}</td>
                            <td>{{ $chat->to->username }}</td>
                            <td>
                                <div class="replyContent">
                                    {!! $chat->message !!}
                                </div>
                                @if(Str::length($chat->message) > 26 || Str::contains($chat->message,['<img']))
                                    <a data-toggle="collapse" href="#chat{{ $chat->id }}" aria-expanded="false">查看更多</a>
                                    <div class="collapse well" id="chat{{ $chat->id }}">{!! $chat->message !!}</div>
                                @endif
                            </td>
                            <td>{{ $chat->created_at }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="text-right">
            <!-- Pager -->
            {!! $chats->appends(Request::except('page', '_pjax'))->render() !!}
        </div>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: function () {
                return {
                    chat_id:'',
                    from_user_id:'',
                    to_user_id:'',
                    message:'',
                    date_start:"",
                    date_end:"",
                }
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