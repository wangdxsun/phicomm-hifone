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
                        <td>日期</td>
                        <td>每日搜索词总数</td>
                        <td>详情</td>
                    </tr>
                    @foreach ($stats as $value)
                        <tr>
                            <td>{{ $value->date }}</td>
                            <td>{{ $value->cnt }}</td>
                            <td>
                                <a href="/dashboard/stat/search/{{ $value->date }}">详情</a>
                            </td>
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