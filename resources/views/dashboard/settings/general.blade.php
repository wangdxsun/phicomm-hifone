@extends('layouts.dashboard')

@section('content')
@if(isset($sub_menu))
@include('dashboard.partials.sub-sidebar')
@endif
<div class="content-wrapper">
    <div class="header sub-header" id="general">
        <span class="uppercase">
            {{ trans('dashboard.settings.general.general') }}
        </span>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form id="settings-form" name="SettingsForm" class="form-vertical" role="form" action="/dashboard/settings" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                @include('partials.errors')
                <fieldset>
                    <div class="row" >
                            <div class="col-md-6 col-md-offset-1">
                                <label>{{ trans('dashboard.settings.general.auto_audit') }}</label>
                            </div>
                            <div class="pull-right col-md-3">
                                <a data-url="{{ route('dashboard.settings.close') }}" class="btn btn-primary"  data-method="post">{{Config::get('setting.auto_audit') ? '关闭' : '打开'}}</a>
                            </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
@stop