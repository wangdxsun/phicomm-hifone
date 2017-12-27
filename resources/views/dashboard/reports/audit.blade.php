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
                        <td style="width: 50px;">#</td>
                        <td style="width: 100px;">举报类型</td>
                        <td style="width: 250px;">帖子标题或回帖内容</td>
                        <td style="width: 100px;">举报人</td>
                        <td style="width: 100px;">举报原因</td>
                        <td style="width: 150px;">举报时间</td>
                        <td style="width: 70px;">操作</td>
                    </tr>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->type }}</td>
                            <td>
                                <div class="replyContent">
                                    <a href="{{ $report->reportable->url }}" target="_blank">{{ $report->reportable->report }}</a>
                                </div>
                                @if(Str::length($report->reportable->report) > 50 || Str::contains($report->reportable->report, ['<img']))
                                    <a data-toggle="collapse" href="#report{{ $report->id }}" aria-expanded="false">查看更多</a>
                                    <div class="collapse well" id="report{{ $report->id }}">{{ $report->reportable->report }}</div>
                                @endif
                            </td>
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
