@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> banner数据统计
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>banner图片</td>
                        <td>累计消息数量</td>
                        <td>累计独立用户数</td>
                        <td>查看</td>
                    </tr>
                    @foreach ($carousels as $carousel)
                        <tr>
                            <td><a href="{{ $carousel->url }}" target="_blank"><img src="{{ $carousel->image }}" style="max-width: 200px; max-height: 50px;"></a></td>
                            <td>{{ $carousel->click_count }}</td>
                            <td>{{ $carousel->view_count }}</td>
                            <td><a href="/dashboard/stat/banner/{{ $carousel->id }}">详情</a></td>
                        </tr>
                    @endforeach
                </table>

            </div>
        </div>
    </div>
@stop
