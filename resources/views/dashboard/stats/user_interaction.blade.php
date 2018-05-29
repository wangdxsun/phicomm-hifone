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
                        <td>每日收藏数</td>
                        <td>每日点赞数</td>
                        <td>每日累计关注(粉丝)数</td>
                    </tr>
                    @foreach ($userStat as $value)
                        <tr>
                            <td>{{ $value->date }}</td>
                            <td>{{ $value->favorite_cnt }}</td>
                            <td>{{ $value->like_cnt }}</td>
                            <td>{{ $value->follow_cnt }}</td>
                        </tr>
                    @endforeach
                </table>


            </div>
        </div>
    </div>
@stop