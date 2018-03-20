@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                 <i class="fa fa-calendar"></i> 操作日志
            </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline pull-right">
                        <div class="form-group">
                            <input type="text" name="query[type]" class="form-control" value="" placeholder="类型">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

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

                </table>

            </div>
        </div>
    </div>
@stop
