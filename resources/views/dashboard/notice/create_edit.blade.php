@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header" id="nodes">
        <span class="uppercase">
              {{ trans(isset($node) ? 'dashboard.notices.edit.title' : 'dashboard.notices.add.title') }}
        </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($node))
                    {!! Form::model($notice, ['route' => ['dashboard.notice.update', $notice->id], 'id' => 'notice-create-form', 'method' => 'patch']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.notice.store','id' => 'notice-create-form', 'method' => 'post']) !!}
                @endif
                @include('partials.errors')
                <fieldset>
                    <div class="form-group">
                        <label>{{ trans('dashboard.notices.title') }}</label>
                        {!! Form::text('notice[title]', isset($notice) ? $notice->title : null, ['class' => 'form-control', 'id' => 'notice-name', 'placeholder' => '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.notices.start_time') }}</label>
                        {!! Form::text('notice[start_time]', isset($notice) ? $notice->start_time : null, ['class' => 'form-control', 'id' => 'notice-slug', 'placeholder' => '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.notices.end_time') }}</label>
                        {!! Form::text('notice[end_time]', isset($notice) ? $notice->end_time : null, ['class' => 'form-control', 'id' => 'notice-slug', 'placeholder' => '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.notices.notice_type.title') }}</label>
                        <select name="notice[type]" class="form-control">
                            <option value="0">请选择分类</option>
                                <option value="1" {{--{{ option_is_selected([$section, 'section_id', isset($notice) ? $notice : null]) }}--}}>{{ trans('dashboard.notices.notice_type.type_1') }}</option>
                            <option value="2" {{--{{ option_is_selected([$section, 'section_id', isset($notice) ? $notice : null]) }}--}}>{{ trans('dashboard.notices.notice_type.type_2') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.notices.content') }}</label>
                        {!! Form::textarea('node[content]', isset($notice) ? $notice->content : null , ['class' => 'form-control',
                                            'rows' => 5,
                                            'style' => "overflow:hidden",
                                            'id' => 'notice-content',
                                            'placeholder' => '']) !!}
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.notice.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop