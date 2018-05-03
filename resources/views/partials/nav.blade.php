<div class="header">
    <nav class="navbar navbar-inverse navbar-fixed-top navbar-default">
        <div class="container">
            <div class="navbar-header" id="navbar-header">
                <a href="/" class="navbar-brand">
                    @if(Config::get('setting.site_logo'))
                        <img src="{{ Config::get('setting.site_logo') }}">
                    @else
                        {{ Config::get('setting.site_name') }}
                    @endif
                </a>
            </div>
            <div id="main-nav-menu">
                <ul class="nav navbar-nav">
                    <li {!! set_active('/') !!}><a href="{!! route('home') !!}"><i class="fa fa-home"></i>
                            <span class="hidden-xs hidden-sm">{!! trans('hifone.home') !!}</span></a></li>
                    <li {!! set_active('thread*',['hidden-sm hidden-xs']) !!}>
                        <a href="{!! route('thread.index') !!}"><i class="fa fa-comments-o"></i> {!! trans('hifone.threads.threads') !!}</a></li>
                    <li {!! set_active('excellent*') !!}>
                        <a href="{!! route('excellent') !!}"><i class="fa fa-diamond"></i>
                            <span class="hidden-xs hidden-sm">{!! trans('hifone.excellent') !!}</span></a></li>
                </ul>
            </div>
            @if(Auth::check())
                <ul class="nav user-bar navbar-nav navbar-right">
                    <li {!! set_active('users*', ['dropdown']) !!}>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ auth()->user->username }}
                            <span class="caret"></span></a>
                        <button class="navbar-toggle" type="button" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="sr-only">Toggle</span> <i class="fa fa-reorder"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li class=""><a href="{{ route('user.home', auth()->user->username) }}"><i class="fa fa-home"></i> {{ trans('hifone.users.profile') }}</a></li>
                            <li><div class='divider'></div></li>
                            <li><a href="{!! route('user.edit', Auth::user()->id) !!}"><i class="fa fa-user"></i> {{ trans('hifone.users.edit.title') }}</a></li>
                            <li><a href="{{ route('user.favorites',auth()->user->id) }}"><i class="fa fa-bookmark"></i> {{ trans('hifone.users.favorites') }}</a></li>
                            <li><a href="{{ route('credit.index')}}"><i class="fa fa-money"></i> {{ trans('hifone.users.credits') }}</a></li>
                            <li class='divider'></li>
                            <li><a data-url="{!! url('auth/logout') !!}" data-method="get" class="confirm-action" data-text="确认要退出吗？"><i class="fa fa-sign-out"></i> {!! trans('hifone.logout') !!}</a></li>
                        </ul>
                    </li>
                </ul>
            @endif

            <ul class="nav navbar-nav navbar-right">
                <li class="nav-search hidden-xs hidden-sm">
                    {!! Form::open(['method'=>'get', 'class'=>'navbar-form form-search active', 'route' => 'search']) !!}
                    <div class="form-group">
                        {!!Form::input('search', 'q', request('q'), ['placeholder'=>trans('hifone.search'),'class'=>'form-control'])!!}
                    </div>
                    <i class="fa fa-search"></i>
                    {!! Form::close() !!}
                </li>
                @if(Auth::check())
                    @if(auth()->user->hasRole(['Founder','Admin']))
                        <li>
                            <a href="/dashboard" data-pjax="no" title="{{ trans('hifone.dashboard') }}"><i class="fa fa-cogs"></i>
                                <span class="hidden-xs hidden-sm">{{ trans('hifone.dashboard') }}</span></a>
                        </li>
                    @endif
                    <li {!! set_active('notification*', ['notification']) !!}>
                        <a href="{!! route('notification.index') !!}" class="notification-count {{ auth()->user->notification_count ? 'new' : null }}"><i class="fa fa-bell"></i><span class="count">{{ auth()->user->notification_count ?: null }}</span></a>
                    </li>
                @else
                    <li {!! set_active('phicomm/register') !!}>
                        <a href="{{ route('auth.register') }}" id="signup-btn">{!! trans('hifone.signup') !!}</a>
                    </li>
                    <li {!! set_active('phicomm/login') !!}>
                        <a href="{{ route('auth.login') }}" id="login-btn">{!! trans('hifone.login.login') !!}</a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</div>