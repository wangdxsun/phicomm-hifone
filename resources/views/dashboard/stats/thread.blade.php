@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-file"></i> 每日新增发帖数目统计
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日新增帖子数</td>
                        <td>每日路由App反馈帖子数</td>
                        <td>每日斐讯社区发帖数</td>
                    </tr>
                    @foreach ($statsArr as $key => $value)
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{  $value['total'] }}</td>
                            <td>{{ $value['feedback']}}</td>
                            <td>{{ $value['forum']}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop