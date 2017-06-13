@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-star"></i> 经验值管理
            <div class="clearfix"></div>
        </div>
        @include('partials.errors')
        <div class="row">
            <div class="col-sm-12">
                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">#</td>
                        <td >经验值规则</td>
                        <td >周期</td>
                        <td>奖励次数</td>
                        <td>奖励经验值</td>
                        <td style="width:10%">操作</td>
                    </tr>
                    @foreach($credit_rules as $credit_rule)
                        <tr>
                            <td>{{ $credit_rule->id }}</td>
                            <td>{{ $credit_rule->name }}</td>
                            <td>{{ $credit_rule->type_str }}</td>
                            <td>{{ $credit_rule->times }}</td>
                            <td>{{ $credit_rule->reward }}</td>
                            <td>
                                <a href="/dashboard/creditRule/{{ $credit_rule->id }}/edit"><i class="fa fa-pencil"></i></a>
                                {{--<a data-url="/dashboard/creditRule/{{ $credit_rule->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>--}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop