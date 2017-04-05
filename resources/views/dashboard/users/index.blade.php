@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            <i class="fa fa-user"></i> 用户管理
            @if(Auth::user()->can('new_user'))
                <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.user.create') }}">新增用户</a>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
            <div class="toolbar">
                <form class="form-inline">
                    <select class="form-control selectpicker" name="user[id]">
                        <option value="" selected>用户ID</option>
                        @foreach ($all_users as $user)
                            <option value="{{ $user->id }}">{{ $user->id }}</option>
                        @endforeach
                    </select>
                    <select class="form-control selectpicker" name="user[username]">
                        <option value="" selected>用户名</option>
                        @foreach ($all_users as $user)
                            <option value="{{ $user->username }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                    <select class="form-control selectpicker" name="user[regip]">
                        <option value="" selected>注册IP</option>
                        @foreach ($all_users as $user)
                            <option value="{{ $user->regip }}">{{ $user->regip }}</option>
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
                    <td>用户组</td>
                    <td>发帖数</td>
                    <td>积分</td>
                    <td>注册时间</td>
                    <td>注册IP</td>
                    <td>操作人</td>
                    <td>操作时间</td>
                    <td>操作</td>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td><img src="{{ $user->avatar_small }}" style="width: 20px; height: 20px;"></td>
                        <td><a href="{{ route('user.show',['id'=>$user->id]) }}" target="_blank">{{ $user->username }}</a></td>
                        <td>{{ $user->roles }}</td>
                        <td>{{ $user->thread_count }}</td>
                        <td>{{ $user->score }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->regip }}</td>
                        <td>{{ $user->lastOpUser->username }}</td>
                        <td>{{ $user->last_op_time }}</td>
                        <td>
                            @if(Auth::user()->id <> $user->id)
                                @if(Auth::user()->can('edit_users'))
                                    <a href="/dashboard/user/{{ $user->id }}/edit" title="编辑"><i class="fa fa-pencil"></i></a>
                                    <a data-url="/dashboard/user/{{ $user->id }}/comment" data-method="post" title="禁止发言"><i class="{{ $user->comment }}"></i></a>
                                    <a data-url="/dashboard/user/{{ $user->id }}/login" data-method="post" title="禁止登录"><i class="{{ $user->login }}"></i></a>
                                    {{--<a data-url="/dashboard/user/{{ $user->id }}" data-method="delete" class="confirm-action"><i class="fa fa-trash"></i></a>--}}
                                @endif
                            @endif
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
        <div class="col-sm-12">
    </div>
@stop