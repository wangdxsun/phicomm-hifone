@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> banner数据详情
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline pull-right">
                        <div class="form-group">
                            <input type="date" name="begin_day" class="form-control" value="" placeholder="开始时间">
                        </div>
                        <div class="form-group">
                            <input type="date" name="end_day" class="form-control" value="" placeholder="结束时间">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>消息数量</td>
                        <td>独立用户数</td>
                    </tr>
                    @foreach ($daily_stats as $dailyStat)
                        <tr>
                            <td>{{ $dailyStat->date }}</td>
                            <td>{{ $dailyStat->click_count }}</td>
                            <td>{{ $dailyStat->view_count }}</td>
                        </tr>
                    @endforeach
                </table>
                {{--分页--}}
                <div>
                    <div class="pull_right" align="right">
                        {{ $daily_stats->render() }}
                    </div>
                </div>

            </div>
        </div>

    </div>



@stop
