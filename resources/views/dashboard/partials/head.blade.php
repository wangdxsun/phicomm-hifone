<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <meta name="env" content="{{ app('env') }}">
    <meta name="token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="/images/favicon.ico">
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">

    {{--<link rel="apple-touch-icon" href="/img/apple-touch-icon.png">--}}
    <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/apple-touch-icon-152x152.png">
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">

    <title>{{ $sub_header or $site_title }}</title>

    <link rel="stylesheet" href="{{ elixir('dist/css/all.css') }}">
    <script src="{{ elixir('dist/js/all.js') }}"></script>
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script type="text/javascript">
        var Global = {};
        Global.locale = 'zh-CN';
        Hifone.Config = {
            'cdnDomain': '{{ cdn() }}',
            'user_id': '{{ Auth::user() ? Auth::user()->id : 0 }}',
            'upload_image' : '{{ route('upload_image') }}',
            'token': '{{ csrf_token() }}'
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