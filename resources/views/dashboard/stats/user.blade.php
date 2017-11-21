@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-user"></i> 每日新增用户数目统计
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日新增用户数</td>
                    </tr>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->date}}</td>
                            <td>{{ $user->cnt }}</td>
                        </tr>
                    @endforeach
                </table>
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>总用户数</td>
                        <td>{{ $usersCount }}</td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
@stop