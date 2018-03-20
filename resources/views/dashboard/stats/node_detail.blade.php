@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper" id="app">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> 版块数据详情
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <form class="form-inline pull-right">
                    <el-input :value="date_end_str" type="hidden" resize=" both"  style="width: 60px; height: 10px;" name="node[date_end]"></el-input>
                    <el-input :value="date_start_str"  type="hidden" resize=" both"  style="width: 60px; height: 10px;" name="node[date_start]"></el-input>
                    <el-date-picker type="date" placeholder="开始时间" v-model="date_start"></el-date-picker>
                    <el-date-picker type="date" placeholder="结束时间" v-model="date_end"></el-date-picker>
                    <button class="btn btn-success">搜索</button>
                </form>

                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>新增帖子数量</td>
                        <td>新增回复数量</td>
                    </tr>
                    @foreach($statsArr as $key => $value)
                        <tr>
                            <td>{{ $value['date'] }}</td>
                            <td>{{ $value['thread_count'] }}</td>
                            <td>{{ $value['reply_count'] }}</td>
                        </tr>
                    @endforeach
                </table>

                <div>
                    <div class="text-left">
                        <span>
                            累计新增帖子总数：{{ $allThreadsCount }}
                        </span>
                    </div>
                    <div>
                        <span>
                            累计新增回复总数：{{ $allRepliesCount }}
                        </span>
                    </div>
                </div>

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
                    return this.date_start === '' ? '' : this.date_start.format('yyyy-MM-dd');
                },
                date_end_str: function () {
                    return this.date_end === '' ? '' : this.date_end.format('yyyy-MM-dd');
                }
            }
        });
    </script>
@stop