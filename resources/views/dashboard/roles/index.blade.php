@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">
        <span class="uppercase">
            <i class="ion ion-ios-browsers-outline"></i> 角色管理
        </span>
        <a class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.role.create') }}">
            新增角色
        </a>
        <div class="clearfix"></div>
    </div>
    @include('partials.errors')
    <div class="row">
        <div class="col-sm-12 striped-list" id="item-list" data-item-name="section">
            @forelse($roles as $role)
            <div class="row striped-list-item" data-item-id="{{ $role->id }}">
                <div class="col-xs-2">{{ $role->display_name }}</div>
                <div class="col-xs-8">{{ $role->permissions }}</div>
                <div class="col-xs-2 text-right">
                    <a href="/dashboard/role/{{ $role->id }}/edit" class="btn btn-default btn-sm">{{ trans('forms.edit') }}</a>
                    <a data-url="/dashboard/role/{{ $role->id }}/delete" class="btn btn-danger btn-sm confirm-action" data-method="delete">{{ trans('forms.delete') }}</a>
                </div>
            </div>
            @empty
            <div class="list-group-item"><a href="{{ route('dashboard.role.create') }}">新增角色</a></div>
            @endforelse
        </div>
    </div>
</div>
@stop
