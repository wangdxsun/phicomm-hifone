@extends('layouts.default')
@section('title')
    {{ trans('hifone.login.register') }}
@stop
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">重置密码</div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="/phicomm/reset">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone') }}" placeholder="手机号" required autofocus>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="verifyCode" name="verifyCode" type="text" placeholder="验证码" class="form-control" required>
                                    <div id="checkNum" onclick="sendVerifyCode()" class="btn input-group-addon">获取验证码</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input id="password" name="password" type="password" class="form-control" placeholder="新密码" required>
                            </div>
                            <div class="form-group">
                                <input id="password_comfirm" type="password" class="form-control" placeholder="{{ trans('hifone.users.password_confirmation') }}" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">提交</button>
                                <a href="/" class="btn btn-default">{{ trans('forms.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer">
                        {!! "已注册斐讯云账号，请点击 <a href='/phicomm/login'>这里</a> 进行登录。" !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    function sendVerifyCode() {
        $.post('/phicomm/verifyCode', {phone: $('#phone').val()}).then(function(res) {
            if(res.code > 0){
                swal({
                    type: 'error',
                    title: res.msg,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                swal({
                    type: 'success',
                    title: res.msg,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }
</script>