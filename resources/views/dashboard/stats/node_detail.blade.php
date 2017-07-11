@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> 版块数据详情
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>新增帖子数量</td>
                        <td>新增回复数量</td>
                    </tr>
                    @foreach($daily_stats as $dailyNode)
                        <tr>
                            <td>{{ $dailyNode->date }}</td>
                            <td>{{ $dailyNode->thread_count }}</td>
                            <td>{{ $dailyNode->reply_count }}</td>
                        </tr>
                    @endforeach
                </table>
                {{--分页--}}
                <div>
                    <div class="pull_right">
                        {{ $daily_stats->render() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop