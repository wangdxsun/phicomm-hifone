@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">
        <i class="fa fa-user"></i> {{ $page_title }}
        @if (Auth::user()->can('user_group'))
            <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.group.users.create') }}">新增用户组</a>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered table-striped table-condensed">
                <tbody>
                <tr class="head">
                    <td >#</td>
                    <td>用户组名称</td>
                    <td>权限列表</td>
                    <td>经验值范围</td>
                    <td>创建人</td>
                    <td>创建时间</td>
                    <td style="width: 110px">操作</td>
                </tr>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ $role->permissions }}</td>
                        <td>{{ $role->credit_low . ' ~ ' . $role->credit_high }}</td>
                        <td>{{ $role->user->username }}</td>
                        <td>{{ $role->created_at }}</td>
                        <td>
                            @if (Auth::user()->can('user_group'))
                            <a href="/dashboard/group/users/{{ $role->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                            <a data-url="/dashboard/group/users/{{ $role->id }}" data-method="delete" class="confirm-action" title="删除"><i class="fa fa-trash"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
