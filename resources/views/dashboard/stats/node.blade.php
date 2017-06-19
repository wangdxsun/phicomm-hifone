@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> 版块数据统计
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tr class="head">
                        <td>版块</td>
                        <td>帖子数量</td>
                        <td>回帖数量</td>
                        <td>详情</td>
                    </tr>
                    @foreach ($nodes as $node)
                        <tr>
                            <td>{{ $node->name }}</td>
                            <td>{{ $node->thread_count }}</td>
                            <td>{{ $node->reply_count }}</td>
                            <td><a href="/dashboard/stat/node/{{ $node->id }}">详情</a></td>
                        </tr>
                    @endforeach
                </table>

            </div>
        </div>
    </div>
@stop
