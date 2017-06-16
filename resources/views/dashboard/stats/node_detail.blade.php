@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> node数据详情
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
                        <td>新增帖子数量</td>
                        <td>新增回复数量</td>
                    </tr>
                    @foreach($dailyNodes as $dailyNode)
                        <tr>
                            <td>{{ $dailyNode->date }}</td>
                            <td>{{ $dailyNode->thread_count }}</td>
                            <td>{{ $dailyNode->reply_count }}</td>
                        </tr>
                    @endforeach
                </table>

            </div>
        </div>
    </div>
@stop