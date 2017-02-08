@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="/css/phicommLogin.css">
@endsection
@section('title')
    {{ trans('hifone.login.login') }}
@stop

@section('content')

    <div class="content">
        <div class="inputBox">
            <div class="userNameImg"></div>
            <input id="phone" type="text" placeholder="手机号" class="userName" name="username" required autofocus>
        </div>
        <div class="inputBox">
            <div class="passwordImg"></div>
            <form method="POST" action="" name="forms">
                <div id="box">
                    <input id="password" type="password" placeholder="密码" class="password" name="password" required>
                </div>
                <div id="eyes">
                    <a href="javascript:showps()"></a>
                </div>
            </form>
        </div>
        <a class="forgetPW" href="forgetPwd.html">忘记密码？</a>

        <div class="loginS">
            <a onclick="phicommLogin()">登录</a>
        </div>
        <div class="loginX">
            <a href="/phicomm/register">注册</a>
        </div>
    </div>
    <script>
        function phicommLogin() {
            $.post('phicomm/login', {
                phone: $("#phone").val(),
                password: $("#password").val(),
                _token: "{{ csrf_token() }}"
            }).then(function(res) {
                if (res.error > 0) {
                    alert(res.message);
                } else {
                    if (result.data.bind == 1) {
                        //如果已经关联过账号就跳转到首页
                        location.href = '/';
                    } else {
                        location.href = '/bbs/create';
                    }
                }
            });
        }
    </script>
@stop