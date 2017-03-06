@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
        <span class="uppercase">
            <i class="ion ion-ios-browsers-outline"></i> 积分管理
        </span>
            <div class="clearfix"></div>
        </div>
        @include('partials.errors')
        <div class="row">
            @include('partials.errors')
            <table class="table table-bordered table-striped table-condensed">
                <tbody>
                <tr class="head">
                    <td class="first">#</td>
                    <td >积分规则</td>
                    <td >频率</td>
                    <td>奖励积分</td>
                    <td style="width:10%">操作</td>
                </tr>
                @foreach($rules as $rule)
                    <tr>
                        <td>{{ $rule->id }}</td>
                        <td>{{ $rule->name }}</td>
                        <td>{{ $rule->frequency }}</td>
                        <td>{{ $rule->reward }}</td>
                        <td>
                            <a href="/dashboard/credit/{{ $rule->id }}/edit"><i class="fa fa-pencil"></i></a>
                            <a data-url="/dashboard/credit/{{ $rule->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop