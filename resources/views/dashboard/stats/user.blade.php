@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">

        @if(isset($sub_nav))
            @include('dashboard.partials.sub-nav')
        @endif
        <div class="row">
            <div class="col-sm-12">

                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日新增用户</td>
                        <td>每日发帖用户</td>
                        <td>每日回帖用户</td>
                        <td>每日贡献内容用户</td>
                    </tr>
                    @foreach ($userStat as $value)
                        <tr>
                            <td>{{ $value->date }}</td>
                            <td>{{ $value->user_cnt }}</td>
                            <td>{{ $value->thread_user_cnt }}</td>
                            <td>{{ $value->reply_user_cnt }}</td>
                            <td>{{ $value->contribute_user_cnt }}</td>
                        </tr>
                    @endforeach
                </table>


            </div>
        </div>
    </div>
@stop