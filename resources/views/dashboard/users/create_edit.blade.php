@extends('layouts.dashboard')

@section('content')
    <div class="header">
        <div class="sidebar-toggler visible-xs">
            <i class="fa fa-navicon"></i>
        </div>
        <span class="uppercase">
            <i class="fa fa-user"></i> {{ trans('dashboard.users.users') }}
        </span>
        > <small>{{ trans(isset($user) ? 'dashboard.users.edit.title' : 'dashboard.users.add.title') }}</small>
    </div>
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                    @include('partials.errors')
                    @if(isset($user))
                        {!! Form::model($user, ['route' => ['dashboard.user.update', $user->id], 'id' => 'user-edit-form', 'method' => 'patch']) !!}
                    @else
                        {!! Form::open(['route' => 'dashboard.user.store','id' => 'user-create-form', 'method' => 'post']) !!}
                    @endif
                    <fieldset>
                        <div class="form-group">
                            <img src="{{ isset($user) ? $user->avatar : url('/images/discuz_big.gif')}}" class="ImagePreviewBox" style="max-height: 200px; max-width: 200px;">
                        </div>
                        <div class="form-group">
                            <a data-url="/dashboard/user/{{ isset($user) ? $user->id : null }}/avatar" class="btn btn-sm btn-danger confirm-action {{ isset($user) ? ($user->avatar_url ? null : 'hide') : 'hide' }}" data-method="post">恢复默认头像</a>
                        </div>
                        <div class="form-group">
                            <label for="user-username">{{ trans('dashboard.users.username') }}</label>
                            {!! Form::text('user[username]', isset($user) ? $user->username : null, ['class' => 'form-control', 'id' => 'user-username', isset($user) ? 'readonly' : null]) !!}
                        </div>
                        <div class="form-group">
                            <label for="user-role">用户组</label>
                            <select class="form-control small change-role" name="roleId" id="user-role">
                                <option value="0">普通用户</option>
                                @foreach ($roles as $role)
                                    <option value="{{$role->id}}" {{ isset($user) ? ($user->hasRole($role->name) ? 'selected' : false) : false }}>{{$role->display_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user-credit">用户经验值</label>
                            <input type="number" class="form-control" name="user[score]" value="{{ isset($user) ? $user->score : 0 }}" min=0>
                        </div>
                        @if (!isset($user))
                        <div class="form-group">
                            <label for="user-password">{{ trans('dashboard.users.password') }}</label>
                            <input type="password" name="user[password]" class="form-control" min="6" id="user-password">
                        </div>
                        @endif
                    </fieldset>
                    <div class='form-group'>
                        <div class='btn-group'>
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ route('dashboard.user.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop