@extends('layouts.dashboard')

@section('content')
    <div class="header fixed">
        <div class="sidebar-toggler visible-xs">
            <i class="fa fa-navicon"></i>
        </div>
        <span class="uppercase">
            <i class="fa fa-user"></i> {{ trans('dashboard.users.users') }}
        </span>
        <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.user.create') }}">
            {{ trans('dashboard.users.add.title') }}
        </a>
        <div class="clearfix"></div>
    </div>
    <div class="content-wrapper header-fixed">
        <div class="row">
            <div class="col-sm-12">
                <div class="toolbar">
                    <form class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" value="" placeholder="用户名">
                        </div>
                        <button class="btn btn-default">搜索</button>
                    </form>
                </div>

                @include('partials.errors')
                <table class="table table-bordered table-striped table-condensed">
                    <tbody>
                    <tr class="head">
                        <td class="first">#</td>
                        <td>用户名</td>
                        <td>昵称</td>
                        <td>邮箱</td>
                        <td>角色</td>
                        <td>发帖数</td>
                        <td>积分</td>
                        <td>注册时间</td>
                        <td style="width:10%">操作</td>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><a href="{{ route('user.show',['id'=>$user->id]) }}" target="_blank">{{ $user->username }}</a></td>
                            <td>{{ $user->nickname }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->badgeName }}</td>
                            <td>{{ $user->thread_count }}</td>
                            <td>{{ $user->score }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>
                                <a href="/dashboard/user/{{ $user->id }}/edit"><i class="fa fa-pencil"></i></a>
                                <a data-url="/dashboard/user/{{ $user->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <!-- Pager -->
                    {!! $users->appends(Request::except('page', '_pjax'))->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop