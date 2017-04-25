@extends('layouts.default')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('hifone.login.login') }}</div>
                    <div class="panel-body">
                        @if($connect_data)
                            <div class="alert alert-info">
                                {{ trans('hifone.login.oauth.login.note', ['provider' => $connect_data['provider_name'], 'name' => $connect_data['nickname']]) }}
                            </div>
                        @endif
                        <form role="form" method="POST" action="/phicomm/login">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <input class="form-control" name="phone" value="{{ Input::old('phone') }}" placeholder="手机号">
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="密码">
                            </div>
                            @if(!$captcha_login_disabled)
                                @include('partials.captcha')
                            @endif
                            <div class="form-group checkbox">
                                <label for="remember_me">
                                    <input type="checkbox" name="remember">{{ trans('hifone.login.remember') }}
                                </label>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="{{ trans('forms.login') }}" class="btn btn-primary btn-block">
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer">
                        <a href="/phicomm/register">{{ trans('forms.register') }}</a>
                        <a href="/phicomm/forget" class="pull-right">忘记密码?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
