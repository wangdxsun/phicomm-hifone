@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                <i class="fa fa-hand-stop-o"></i> {{ $page_title }}
            </span>
            <div class="clearfix"></div>
        </div>
        @if(isset($sub_menu))
            @include('dashboard.partials.sub-nav')
        @endif
        <div class="row">
            <div class="col-sm-12">
                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td style="width: 30px;">#</td>
                        <td>举报类型</td>
                        <td >帖子标题或回帖内容</td>
                        <td >举报人</td>
                        <td >举报原因</td>
                        <td >举报时间</td>
                        <td style="width: 70px;">操作</td>
                    </tr>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->type }}</td>
                            <td><a href="{{ $report->reportable->url }}" target="_blank">{{ $report->reportable->report }}</a></td>
                            <td><a href="{{ $report->user->url }}">{{ $report->user->username }}</a></td>
                            <td>{{ $report->reason }}</td>
                            <td>{{ $report->created_at }}</td>
                            <td>
                                <a data-url="/dashboard/report/{{$report->id}}/ignore" data-method="post" title="忽略该举报"><i class="fa fa-eye-slash"></i></a>
                                <a data-url="/dashboard/report/{{ $report->id }}/trash" data-method="post" class="need-reason" title="删除被举报的内容"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="text-right">
                    <!-- Pager -->
                    {!! $reports->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
