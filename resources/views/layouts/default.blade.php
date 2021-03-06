<!DOCTYPE html>
<html lang="zh_cn">
	<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no" />
        <title>@yield('title') {{ config('site_name') }}</title>
        <meta name="keywords" content="@if(Config::get('setting.meta_keywords')){{ Config::get('setting.meta_keywords') }}@else{{ 'Hifone,BBS,Forum,PHP,Laravel' }}@endif" />
        <meta name="author" content="@if(Config::get('setting.meta_author')){{ Config::get('setting.meta_author') }}@else{{ 'The Hifone Team' }}@endif" />
        <meta name="description" content="@section('description')" />
        <meta name="generator" content="Hifone">
        <meta name="env" content="{{ app('env') }}">
        <meta name="token" content="{{ csrf_token() }}">
        <link rel="shortcut icon" href="/images/favicon.ico">
        <link rel="alternate" type="application/atom+xml" href="/feed" />
        <link rel="stylesheet" href="{{ elixir('dist/css/all.css') }}">
        {{--<script src="/dist/js/echo.js"></script>--}}
        {{--<script src="http://222.73.156.127:20063/socket.io/socket.io.js"></script>--}}
        <script src="{{ elixir('dist/js/all.js') }}"></script>
        <script type="text/javascript">
            Hifone.Config = {
                'current_user_id' : '{{ Auth::user() ? Auth::user()->id : 0 }}',
                'role' : '{{ Auth::user() ? Auth::user()->role : '未登录'}}',
                'token' : '{{ csrf_token() }}',
                'emoj_cdn' : '{{ cdn() }}',
                'uploader_url' : '{{ route('upload_image') }}',
                'notification_url' : '{{ route('notification.count') }}'
            };
            Hifone.jsLang = {
                'delete_form_title' : '{{ trans('hifone.action_title') }}',
                'delete_form_text' : '{{ trans('hifone.action_text') }}',
                'uploading_file' : '{{ trans('hifone.uploading_file') }}',
                'loading' : '{{ trans('hifone.loading') }}',
                'content_is_empty' : '{{ trans('hifone.content_empty') }}',
                'operation_success' : '{{ trans('hifone.success') }}',
                'error_occurred' : '{{ trans('hifone.error_occurred') }}',
                'button_yes' : '{{ trans('hifone.yes') }}',
                'like' : '{{ trans('hifone.like') }}',
                'dislike' : '{{ trans('hifone.unlike') }}'
            };
        </script>
    </head>
    <body class="forum" data-page="forum">
        @include('partials.errors')
        @include('partials.nav')
		<div id="main" class="main-container container" style="min-height: 620px;">
                {!! $breadcrumb or '' !!}
                @include('partials.top')

				@yield('content')

                @include('partials.bottom')
		</div>
        @include('partials.footer')

	</body>
    {{--百度统计--}}
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?{{ env('BAIDU_STATICS') }}";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</html>
