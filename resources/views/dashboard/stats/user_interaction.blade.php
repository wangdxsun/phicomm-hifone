@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>日期</td>
                        <td>每日收藏数</td>
                        <td>每日点赞数</td>
                        <td>每日累计关注(粉丝)数</td>
                    </tr>
                    @foreach ($statArr as $key => $value)
                        <tr>
                            <td>{{ $value['date'] }}</td>
                            <td>{{ $value['favorite_count'] }}</td>
                            <td>{{ $value['like_count'] }}</td>
                            <td>{{ $value['follow_count'] }}</td>
                        </tr>
                    @endforeach
                </table>


            </div>
        </div>
    </div>
@stop