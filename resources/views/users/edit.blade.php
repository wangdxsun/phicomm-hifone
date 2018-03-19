@extends('layouts.default')

@section('title')
{{ trans('hifone.users.edit_profile') }}@parent
@stop

@section('content')

<div class="users-show">

  <div class="col-md-3 box">
    @include('users.partials.basicinfo')
  </div>

  <div class="col-md-9 left-col">

    <div class="panel panel-default">

      <div class="panel-body ">
        <ul class="nav nav-tabs" role="tablist">
          <li @if(!$tab) class="active" @endif><a href="{{ route('user.edit',['id'=>$user->id]) }}">{{ trans('hifone.users.info') }}</a></li>
          <li @if($tab=='avatar') class="active" @endif><a href="{{ route('user.edit',['id'=>$user->id,'tab'=>'avatar']) }}">{{ trans('hifone.users.avatar') }}</a></li>
        </ul>
        @if($tab == 'avatar')
        <form class="form-horizontal" method="post" action="/settings/update-avatar" enctype="multipart/form-data" id="avatar-form">
                {{ csrf_field() }}
                <h5><i class="fa fa-image"></i>&nbsp;&nbsp;{{ trans('hifone.users.edit_avatar') }}</h5><hr>
                <div class="form-group">
                    <div class="col-sm-4 avatar-setting-container">
                       <img src="{{Auth::user()->avatar}}" id="avatar" class="upload-btn" style="cursor: pointer;">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 status-post-submit">
                    <button type="button" class="btn btn-primary upload-btn">
                        {{ trans('hifone.users.upload_avatar') }}
                    </button>
                    <span class="loading"></span>
                    <input type="file" accept="image/*" id="avatarinput" name="avatar" class="hide">
                    <span class="help-block">
                        {{ trans('hifone.users.upload_avatar_help') }}</span>
                    <button type="submit" class="btn btn-primary hidden" id="avatarinput-submit">更新</button>
                    </div>
                </div>
        </form>
      @else
      {!! Form::model($user, ['route' => ['user.update', $user->id], 'method' => 'patch']) !!}
          <h5><i class="fa fa-tasks"></i>&nbsp;&nbsp;{{ trans('hifone.users.info') }}</h5>

          <div class="form-group">
            {!! Form::text('location', null, ['class' => 'form-control', 'placeholder' => trans('hifone.users.location')]) !!}
            <div class="help-block">{{ trans('hifone.users.location_help') }}</div>
          </div>
          <div class="form-group">
            {!! Form::text('company', null, ['class' => 'form-control', 'placeholder' => trans('hifone.users.company')]) !!}
          </div>
          <div class="form-group">
            {!! Form::text('website', null, ['class' => 'form-control', 'placeholder' => trans('hifone.users.website')]) !!}
          </div>
          <div class="form-group">
            {!! Form::textarea('signature', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => trans('hifone.users.signature')]) !!}
          </div>
          <div class="form-group">
            {!! Form::textarea('bio', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => trans('hifone.users.bio')]) !!}
          </div>
          <div class="form-group status-post-submit">
            {!! Form::submit(trans('forms.update'), ['class' => 'btn btn-primary', 'id' => 'user-edit-submit']) !!}
          </div>
        {!! Form::close() !!}
      @endif
      </div>
    </div>
  </div>


</div>

@stop
