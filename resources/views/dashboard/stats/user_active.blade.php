@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        @if(isset($sub_nav))
            @include('dashboard.partials.sub_nav')
        @endif
        <div class="row">
            <div class="col-sm-12">

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日活跃参与用户数</td>
                        <td>每日活跃参与新用户数</td>
                        <td>每日活跃参与老用户数</td>
                    </tr>
                    @foreach ($statArr as $key => $value)
                        <tr>
                            <td>{{ $value['date'] }}</td>
                            <td>{{ $value['active_user_cnt'] }}</td>
                            <td>{{ $value['new_user_cnt'] }}</td>
                            <td>{{ $value['old_user_cnt'] }}</td>
                        </tr>
                    @endforeach
                </table>


            </div>
        </div>
    </div>
@stop