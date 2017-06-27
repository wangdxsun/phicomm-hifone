@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> 操作日志
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div class="toolbar">
                    <form class="form-inline">
                        <select class="form-control" name="log[user_id]">
                            <option value="" selected>操作人</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                            @endforeach
                        </select>
                        <select class="form-control" name="log[logable_type]" style="max-width: 300px">
                            <option value="" selected>操作对象</option>
                            @foreach ($logable_types as $key =>$logable_type)
                                <option value="{{ $key }}">{{ $logable_type }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="log[logable_id]" class="form-control" value="" placeholder="操作对象ID">
                        <select class="form-control " name="log[operation]">
                            <option value="" selected>全部类型</option>
                            @foreach ($operations as $operation)
                                <option value="{{ $operation->operation }}">{{ $operation->operation }}</option>
                            @endforeach
                        </select>
                        <input name="log[date_start]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="开始时间">
                        <input name="log[date_end]" size="10" type="text" class="form_date form-control" data-date-format="yyyy-mm-dd" placeholder="结束时间">
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>编号</td>
                        <td style="width:20%">操作人</td>
                        <td>操作对象</td>
                        <td>操作对象ID</td>
                        <td>操作类型</td>
                        <td>原因</td>
                        <td>操作时间</td>
                    </tr>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>{{ $log->user->username }}</td>
                            <td>{{ $log->object_type }}</td>
                            <td>{{ $log->logable_id }}</td>
                            <td>{{ $log->operation }}</td>
                            <td>{{ $log->reason }}</td>
                            <td>{{ $log->created_time }}</td>
                        </tr>
                    @endforeach
                </table>
                <div class="text-right">
                    <!-- Pager -->
                    {!! $logs->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop
