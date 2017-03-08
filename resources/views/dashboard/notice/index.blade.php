@extends('layouts.dashboard')

@section('content')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                <i class="fa fa-file-text-o"></i> {{--{{ $sub_header }}--}}公告管理
            </span>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        @if(isset($sub_menu))
        @include('dashboard.partials.sub-nav')
        @endif
        <div class="content-wrapper">
                <div class="header sub-header">
                    <button class="notice_tab" style="color: green">管理</button>
                    <button class="add_content_tab" style="color: red">增加</button>
                    <div class="clearfix"></div>
                </div>
                @include('partials.errors')
                <div class="row">
                    <div class="col-sm-12 striped-list" id="item-list" data-item-name="section">
                        <div class="notice">
                            <div class="toolbar">
                                <form class="form-inline">
                                    <div class="form-group">
                                        <input type="text" name="q" class="form-control" value="" placeholder="标题">
                                    </div>
                                    <button class="btn btn-default">搜索</button>
                                </form>
                            </div>
                            <table class="table table-bordered table-striped table-condensed">
                                <tbody>
                                <tr class="head">
                                    <td class="first">#</td>
                                    <td>标题</td>
                                    <td style="width:20%">内容</td>
                                    <td>公告类型</td>
                                    <td>起始时间</td>
                                    <td>终止时间</td>
                                    <td style="width:10%">操作</td>
                                </tr>
                                @foreach($notices as $notice)
                                    <tr>
                                        <td>复选框</td>
                                        <td>
                                            <a target="_blank" href="{{ $notice->url }}"><i class="{{ $notice->icon }}"></i> {{ $notice->title }}</a>
                                        </td>
                                        <td>{{ $notice->content }}</td>
                                        <td>{{ $notice->type }}</td>
                                        <td>
                                            {{ $notice->created_at }}
                                        </td>
                                        <td>
                                            {{ $notice->updated_at }}
                                        </td>
                                        <td>
                                            <a data-url="" data-method="post" class="confirm-action"><i class="fa fa-check"></i></a>
                                            <a data-url="" data-method="post" class="confirm-action"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="add_content">
                            <div class="col-sm-12">
                                <form id="settings-form" name="notice" class="form-vertical" role="form" action="/dashboard/notice" method="POST">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    @include('partials.errors')
                                    <fieldset>
                                        <input type="hidden" class="form-control" name="user_id" value="{{$user_id}}" required>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label>{{ trans('dashboard.notices.title') }}</label>
                                                    <input type="text" class="form-control" name="title" value="" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label>{{ trans('dashboard.notices.start_time') }}</label>
                                                    <input type="text" class="form-control" name="start_time" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label>{{ trans('dashboard.notices.end_time') }}</label>
                                                    <input type="text" class="form-control" name="end_time" value="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label>{{ trans('dashboard.notices.notice_type.title') }}</label>
                                                    <label><input name="type" type="radio" value="1" />{{ trans('dashboard.notices.notice_type.type_1') }} </label>
                                                    <label><input name="type" type="radio" value="2" />{{ trans('dashboard.notices.notice_type.type_2') }} </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label>{{ trans('dashboard.notices.content') }}</label>
                                                    <div class='markdown-control'>
                                                        <textarea name="content" class="form-control autosize" rows="4" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </fieldset>

                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </div>

    <style>
        .add_content{
            display: none;
        }
    </style>

    <script type="text/javascript">
        $(function () {
            var noticeTab = $('.notice_tab');
            var addContentTab = $('.add_content_tab');
            var notice = $('.notice');
            var addContent = $('.add_content');
            noticeTab.on('click',function () {
                notice.css('display','block');
                addContent.css('display','none');
                window.location.reload();
            });
            addContentTab.on('click',function () {
                addContent.css('display','block');
                notice.css('display','none');
            });
        });
    </script>
@stop
