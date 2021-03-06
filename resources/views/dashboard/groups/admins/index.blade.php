@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">
        <i class="fa fa-users"></i> {{ $page_title }}
        @if (Auth::user()->can('user_group'))
            <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.group.admin.create') }}">新增管理组</a>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered table-striped table-condensed">
                <tbody>
                <tr class="head">
                    <td>编号</td>
                    <td style="width: 100px">管理组名称</td>
                    <td>权限列表</td>
                    <td>创建人</td>
                    <td style="width: 150px">创建时间</td>
                    <td style="width: 70px">操作</td>
                </tr>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ $role->permissions }}</td>
                        <td>{{ $role->user->username }}</td>
                        <td>{{ $role->created_at }}</td>
                        <td>
                            @if (Auth::user()->can('user_group'))
                                <a href="/dashboard/group/admin/{{ $role->id }}/edit"><i class="fa fa-pencil" title="编辑"></i></a>
                                @if ($role->display_name != '管理员' && $role->display_name != '版主' && $role->display_name != '实习版主')
                                    <a data-url="/dashboard/group/admin/{{ $role->id }}" data-method="delete" class="confirm-action" title="删除"><i class="fa fa-trash"></i></a>
                                @endif
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
