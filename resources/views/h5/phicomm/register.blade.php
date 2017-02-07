@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="/css/phicommLogin.css">
@endsection
@section('title')
    {{ trans('hifone.login.register') }}
@stop
@section('content')
    <div class="content">
        <div class="inputBox">
            <input id="phone" type="text" placeholder="手机号" class="userName" name="username" required autofocus>
        </div>
        <div class="inputBox" style="position: relative;">
            <input id="verifyCode" type="text" placeholder="验证码" class="password" name="password" required>
            <button id="checkNum" onclick="sendVerifyCode()" class="disabled" disabled>获取验证码</button>
        </div>
        <div class="inputBox" hidden>
            <input type="text" class="form-control" placeholder="昵称" required>
        </div>
        <div class="inputBox">
            <input id="password" type="password" class="form-control" placeholder="密码" required>
        </div>
        <div class="inputBox">
            <input id="password_comfirm" type="password" class="form-control" placeholder="确认密码" required>
        </div>
        <a class="loginS" style="margin-top: 0.96rem" onclick="phicommLogin()">提交</a>
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