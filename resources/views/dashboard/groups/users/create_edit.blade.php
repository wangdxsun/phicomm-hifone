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
            {!! Form::model($role, ['route' => ['dashboard.group.users.update', $role->id], 'method' => 'patch']) !!}
            @else
            {!! Form::open(['route' => 'dashboard.group.users.store', 'method' => 'post']) !!}
            @endif
            <fieldset>
                <div class="form-group">
                    <label>用户组名称</label>
                     {!! Form::text('role[display_name]', isset($role) ? $role->display_name : null, ['class' => 'form-control', 'required']) !!}
                </div>
                <div class="form-group">
                    <label>用户组描述</label>
                    {!! Form::text('role[description]', isset($role) ? $role->description : null, ['class' => 'form-control']) !!}
                </div>
                <div class="form-group">
                    <label>经验值范围</label>
                    <div class="form-inline">
                        <div class="input-group">
                            <input type="number" name="role[credit_low]" value="{{ isset($role) ? $role->credit_low : 0 }}" class="form-control" placeholder="最小值" required>
                            <div class="input-group-addon">至</div>
                            <input type="number" name="role[credit_high]" value="{{ isset($role) ? $role->credit_high : 0 }}" class="form-control"  placeholder="最大值" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">

                    {{--@foreach($permissions as $permission)--}}
                    {{--<div class="checkbox">--}}
                        {{--<label>--}}
                            {{--{!! Form::checkbox('permissions[]', $permission->id, isset($role) ? ($role->hasPermission($permission) ? true : false) : false) !!}--}}
                            {{--{{ $permission->display_name }}--}}
                        {{--</label>--}}
                    {{--</div>--}}
                    {{--@endforeach--}}
                    <label>权限列表</label>
                    <div>
                        <input id="selectAll" type="checkbox">
                        <label>{{ "全选" }}</label>
                    </div>
                    @foreach($permissions as $permission)
                        <div>
                            <input class="checkAll" type="checkbox"  name="batch[]" value="{{ $permission->id }}">
                            <label>
                                {{ $permission->display_name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="form-group">
                    <input type="hidden" name="role[type]" value="{{ \Hifone\Models\Role::USER }}">
                    <input type="hidden" name="role[user_id]" value="{{ Auth::user()->id }}">
                </div>
            </fieldset>

            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                        <a class="btn btn-default" href="{{ back_url('dashboard.group.users.index') }}">{{ trans('forms.cancel') }}</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop