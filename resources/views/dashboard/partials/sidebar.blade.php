<div class="sidebar">
    <div class="sidebar-inner">
        <div class="profile">
            <a href="{{ url('dashboard/user') }}">
                <span class="avatar"><img src="{{ $current_user->avatar }}"></span>
            </a>
            <a href="{{ url('dashboard/user') }}">
                <h4 class="username">{{ $current_user->username }}</h4>
            </a>
        </div>
        <div class="clearfix"></div>
        <hr />
        <ul>
            <li {!! set_active('dashboard') !!}>
                <a href="{{ route('dashboard.index') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>系统概况</span>
                </a>
            </li>
            <li {!! set_active('dashboard/thread*') !!}>
                <a href="{{ route('dashboard.thread.audit') }}">
                    <i class="fa fa-file-o"></i>
                    <span>话题管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/reply*') !!}>
                <a href="{{ route('dashboard.reply.audit') }}">
                    <i class="fa fa-comments-o"></i>
                    <span>回帖管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/user*') !!}>
                <a href="{{ route('dashboard.user.index') }}">
                    <i class="fa fa-user"></i>
                    <span>用户管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/group*') !!} >
                <a href="{{ route('dashboard.group.users.index') }}">
                    <i class="fa fa-users"></i>
                    <span>用户组管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/word*') !!}>
                <a href="{{ route('dashboard.word.index') }}">
                    <i class="fa fa-filter"></i>
                    <span>敏感词过滤</span>
                </a>
            </li>
            <li {!! set_active('dashboard/notice*') !!}>
                <a href="{{ route('dashboard.notice.index') }}">
                    <i class="fa fa-bullhorn"></i>
                    <span>公告管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/creditRule*') !!}>
                <a href="{{ route('dashboard.creditRule.index') }}">
                    <i class="fa fa-star"></i>
                    <span>积分管理</span>
                </a>
            </li>
            {{--<li {!! set_active('dashboard/page*') !!} {!! set_active('dashboard/photo*') !!}>--}}
                {{--<a href="{{ route('dashboard.photo.index') }}">--}}
                    {{--<i class="fa fa-image"></i>--}}
                    {{--<span>其他管理</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            <li {!! set_active('dashboard/carousel*') !!}>
                <a href="{{ route('dashboard.carousel.index') }}">
                    <i class="fa fa-image"></i>
                    <span>轮播管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/node*') !!} {!! set_active('dashboard/section*') !!}>
                <a href="{{ route('dashboard.node.index') }}">
                    <i class="fa fa-sitemap"></i>
                    <span>板块管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/report*') !!}>
                <a href="{{ route('dashboard.report.index') }}">
                    <i class="fa fa-hand-stop-o"></i>
                    <span>举报管理</span>
                </a>
            </li>
            <li {!! set_active('dashboard/adspace*') !!} {!! set_active('dashboard/advertisement*') !!} {!! set_active('dashboard/adblock*') !!}>
                <a href="{{ route('dashboard.advertisement.index') }}">
                    <i class="fa fa-audio-description"></i>
                    <span>{{ trans('dashboard.advertisements.advertisements') }}</span>
                </a>
            </li>
            <li {!! set_active('dashboard/settings*') !!}>
                <a href="{{ route('dashboard.settings.general') }}">
                    <i class="fa fa-gears"></i>
                    <span>
                        {{ trans('dashboard.settings.settings') }}
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/') }}">
                    <i class="fa fa-desktop"></i>
                    <span>返回首页</span>
                </a>
            </li>
        </ul>
    </div>
</div>