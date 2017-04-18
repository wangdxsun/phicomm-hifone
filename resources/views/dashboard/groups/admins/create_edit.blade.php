@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header">{{ $sub_header }}</div>
    <div class="row">
        <div class="col-sm-12">
            @if(isset($role))
            {!! Form::model($role, ['route' => ['dashboard.group.admin.update', $role->id], 'method' => 'patch']) !!}
            @else
            {!! Form::open(['route' => 'dashboard.group.admin.store', 'method' => 'post']) !!}
            @endif
            @include('partials.errors')
            <fieldset>
                <div class="form-group">
                    <label>管理组名称</label>
                     {!! Form::text('role[display_name]', isset($role) ? $role->display_name : null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    <label>管理组描述</label>
                    {!! Form::text('role[description]', isset($role) ? $role->description : null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    <label>权限列表</label>
                    @foreach($permissions as $permission)
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('permissions[]', $permission->id, isset($role) ? ($role->hasPermission($permission) ? true : false) : false) !!}
                            {{ $permission->display_name }}
                        </label>
                    </div>
                    @endforeach
                </div>
                <div class="form-group">
                    <input type="hidden" name="role[type]" value={{ \Hifone\Models\Role::ADMIN }}>
                    <input type="hidden" name="role[user_id]" value="{{ Auth::user()->id }}">
                </div>
            </fieldset>

            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                        <a class="btn btn-default" href="{{ back_url('dashboard.group.admin.index') }}">{{ trans('forms.cancel') }}</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop