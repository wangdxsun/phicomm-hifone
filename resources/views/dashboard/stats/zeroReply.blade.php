@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-file"></i> 零回复统计
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="pull-right">
                    <span class="uppercase">
                        当前页零回复帖子总数:{{ $recentZeroReplyThreadCount }}
                    </span>
                    </div>
                </div>

                <div class="row">
                    <div class="pull-right">
                        <span class="uppercase">
                            截止现在零回复帖子总数:{{ $allZeroReplyThreadCount }}
                        </span>
                    </div>
                </div>

                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日零回复帖子数</td>
                        <td>App反馈零回复帖子数</td>
                        <td>社区零回复帖子数</td>
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