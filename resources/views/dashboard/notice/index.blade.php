@extends('layouts.dashboard')

@section('content')
    <div class="header fixed">
        <div class="sidebar-toggler visible-xs">
            <i class="fa fa-navicon"></i>
        </div>
        <span class="uppercase">
             <i class="fa fa-bullhorn"></i>  {{ trans('dashboard.notices.notice') }}
        </span>
        <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.notice.create') }}">
            {{ trans('dashboard.notices.add.title') }}
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="content-wrapper header-fixed">
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" value="" placeholder="标题">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">#</td>
                        <td>标题</td>
                        <td style="width:20%">内容</td>
                        <td>公告类型</td>
                        <td>起始时间</td>
                        <td>终止时间</td>
                        <td style="width:10%">操作</td>
                    </tr>
                    @foreach($notices as $notice)
                        <tr>
                            <td>{{ $notice->id }}</td>
                            <td><a href="{{--{{ route('$notice.show',['id'=>$notice->id]) }}--}}" target="_blank">{{ $notice->title }}</a></td>
                            <td>{{ $notice->content }}</td>
                            <td>{{ $notice->type }}</td>
                            <td>{{ $notice->start_time }}</td>
                            <td>{{ $notice->end_time }}</td>
                            <td>
                                <a href="/dashboard/user/{{ $notice->id }}/edit"><i class="fa fa-pencil"></i></a>
                                <a data-url="/dashboard/user/{{ $notice->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <!-- Pager -->
                 {{--   {!! $notice->appends(Request::except('page', '_pjax'))->render() !!}--}}
                </div>
            </div>
        </div>
    </div>
@stop