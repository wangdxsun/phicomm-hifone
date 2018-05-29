@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">

        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>热搜名词</td>
                        <td>今日出现次数</td>
                        <td>截止当前总次数</td>
                    </tr>
                    @foreach ($dailyStat as $value)
                        <tr>
                            <td>{{ $value->word }}</td>
                            <td>{{ $value->daily_cnt }}</td>
                            <td>{{ $value->stat_cnt }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@stop