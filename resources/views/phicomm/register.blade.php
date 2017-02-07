@extends('layouts.default')
@section('css')
    {{--<link rel="stylesheet" href="/css/phicommLogin.css">--}}
@endsection
@section('title')
    {{ trans('hifone.login.register') }}
@stop
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ trans('hifone.signup') }}</div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="/auth/register">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <input id="phone" type="text" placeholder="手机号" required autofocus>
                            </div>
                            <div class="form-group">
                                <input id="verifyCode" type="text" placeholder="验证码" class="password" name="password" required>
                                <button id="checkNum" onclick="sendVerifyCode()" class="disabled" disabled>获取验证码</button>
                            </div>
                            <div class="form-group">
                                <input id="password" type="password" placeholder="{{ trans('hifone.users.password') }}" required>
                            </div>
                            <div class="form-group">
                                <input id="password_comfirm" type="password" placeholder="{{ trans('hifone.users.password_confirmation') }}" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ trans('forms.register') }}</button>
                                <a href="/" class="btn btn-default">{{ trans('forms.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer">
                        {!! trans('hifone.login.account_available') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    function sendVerifyCode() {
        $.post('/phicomm/verifyCode', {phone: $('#phone').val()}).then(function(res) {
            if(res.error > 0){
                alert(res.message);
            }
        });
    }
</script>