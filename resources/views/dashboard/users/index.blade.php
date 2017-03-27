@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-user"></i> 用户管理
            <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.user.create') }}">新增用户</a>
        </div>
        <div class="row">
            <div class="toolbar">
                <form class="form-inline">
                    <select class="form-control selectpicker" name="user[id]">
                        <option value="" selected>全部发帖人</option>
                        @foreach ($all_users as $user)
                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-default">搜索</button>
                </form>
            </div>

            @include('partials.errors')
            <table class="table table-bordered table-striped table-condensed">
                <tbody>
                <tr class="head">
                    <td class="first">#</td>
                    <td>头像</td>
                    <td>用户名</td>
                    <td>昵称</td>
                    <td>邮箱</td>
                    <td>角色</td>
                    <td>发帖数</td>
                    <td>积分</td>
                    <td>注册时间</td>
                    <td>注册IP</td>
                    <td>操作</td>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><img src="{{ $user->avatar_small }}" style="width: 20px; height: 20px;"></td>
                        <td><a href="{{ route('user.show',['id'=>$user->id]) }}" target="_blank">{{ $user->username }}</a></td>
                        <td>{{ $user->nickname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles }}</td>
                        <td>{{ $user->thread_count }}</td>
                        <td>{{ $user->score }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->regip }}</td>
                        <td>
                            <a href="/dashboard/user/{{ $user->id }}/edit"><i class="fa fa-pencil"></i></a>
                            <a data-url="/dashboard/user/{{ $user->id }}" data-method="post"><i class="fa fa-comment"></i></a>
                            {{--<a data-url="/dashboard/user/{{ $user->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>--}}
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
@stop