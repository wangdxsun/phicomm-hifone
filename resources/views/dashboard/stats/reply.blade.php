@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-file"></i> 每日新增回帖数目统计
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日新增回帖数</td>
                        <td>每日路由App反馈回复数</td>
                        <td>每日斐讯社区回帖数</td>
                    </tr>
                    @foreach ($stats as $value)
                        <tr>
                            <td>{{ $value->date }}</td>
                            <td>{{ $value->reply }}</td>
                            <td>{{ $value->feedback }}</td>
                            <td>{{ $value->forum }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <div class="text-right">
            {!! $stats->appends(Request::except('page', '_pjax'))->render() !!}
        </div>
    </div>
@stop