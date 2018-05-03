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

            <li  {!! set_active('dashboard/thread*') !!}>
                <a href="{{ route('dashboard.thread.audit') }}">
                    <i class="fa fa-file-o"></i>
                    <span>帖子管理</span>
                </a>
            </li>

            <li  {!! set_active('dashboard/questions*') !!}>
                <a href="{{ route('dashboard.questions.index') }}">
                    <i class="fa fa-file-o"></i>
                    <span>问答管理</span>
                </a>
            </li>

            <li {!! set_active('dashboard/reply*') !!}>
                <a href="{{ route('dashboard.reply.audit') }}">
                    <i class="fa fa-comments-o"></i>
                    <span>回帖管理</span>
                </a>
            </li>
            @if (Auth::user()->hasRole('Admin')  || Auth::user()->hasRole('Founder'))
                <li {!! set_active('dashboard/tag*') !!}>
                    <a href="{{ route('dashboard.tag.type.index') }}">
                        <i class="fa fa-tags"></i>
                        <span>标签管理</span>
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

                <li {!! set_active('dashboard/creditRule*') !!}>
                    <a href="{{ route('dashboard.creditRule.index') }}">
                        <i class="fa fa-star"></i>
                        <span>经验值管理</span>
                    </a>
                </li>

                <li {!! set_active('dashboard/carousel*') !!}>
                    <a href="{{ route('dashboard.carousel.index') }}">
                        <i class="fa fa-image"></i>
                        <span>banner管理</span>
                    </a>
                </li>
                <li {!! set_active('dashboard/node*') !!} {!! set_active('dashboard/section*') !!}>
                    <a href="{{ route('dashboard.node.index') }}">
                        <i class="fa fa-sitemap"></i>
                        <span>版块管理</span>
                    </a>
                </li>
                <li {!! set_active('dashboard/chat*') !!}>
                    <a href="{{ route('dashboard.chat.send') }}">
                        <i class="fa fa-envelope-o"></i>
                        <span>私信群发</span>
                    </a>
                </li>
                <li {!! set_active('dashboard/report*') !!}>
                    <a href="{{ route('dashboard.report.audit') }}">
                        <i class="fa fa-hand-stop-o"></i>
                        <span>举报管理</span>
                    </a>
                </li>
                <li {!! set_active('dashboard/log*') !!}>
                    <a href="{{ route('dashboard.log.index') }}">
                        <i class="fa fa-calendar"></i>
                        <span>操作日志</span>
                    </a>
                </li>
                <li {!! set_active('dashboard/stat*') !!}>
                    <a href="{{ route('dashboard.stat.index') }}">
                        <i class="fa fa-bar-chart"></i>
                        <span>数据统计</span>
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
            @endif

            <li>
                <a href="{{ url('/') }}">
                    <i class="fa fa-desktop"></i>
                    <span>返回首页</span>
                </a>
            </li>
        </ul>
    </div>
</div>