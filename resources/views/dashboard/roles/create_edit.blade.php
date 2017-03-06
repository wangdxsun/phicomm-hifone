@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header" id="sections">
        <span class="uppercase">修改角色</span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            @if(isset($section))
            {!! Form::model($section, ['route' => ['dashboard.role.update', $section->id], 'id' => 'role-create-form', 'method' => 'patch']) !!}
            @else
            {!! Form::open(['route' => 'dashboard.role.store','id' => 'role-create-form', 'method' => 'post']) !!}
            @endif
            @include('partials.errors')
            <fieldset>
                <div class="form-group">
                    <label>角色名称</label>
                     {!! Form::text('section[name]', isset($role) ? $role->display_name : null, ['class' => 'form-control', 'id' => 'section-name', 'placeholder' => '']) !!}
                </div>
                <div class="form-group">
                    <label>角色描述</label>
                    {!! Form::text('section[order]', isset($role) ? $role->description : null, ['class' => 'form-control', 'id' => 'section-order', 'placeholder' => '']) !!}
                </div>
                <div class="form-group">
                    @foreach($permissions as $permission)
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" {{ $role->hasPermission($permission) ? 'checked' : null }}> {{ $permission->display_name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </fieldset>

            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                        <a class="btn btn-default" href="{{ back_url('dashboard.section.index') }}">{{ trans('forms.cancel') }}</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop