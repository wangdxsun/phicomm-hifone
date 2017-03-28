@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header" id="words">
        <span class="uppercase">
              {{ trans(isset($word) ? 'dashboard.words.edit.title' : 'dashboard.words.add.title') }}
        </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($word))
                    {!! Form::model($word, ['route' => ['dashboard.word.update', $word->id], 'id' => 'word-create-form', 'method' => 'patch']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.word.store','id' => 'word-create-form', 'method' => 'post']) !!}
                @endif
                @include('partials.errors')
                <fieldset>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.type.title') }}</label>
                        {!!  Form::select('word[type]', ['政治' => trans('dashboard.words.type.type_1'),'广告' => trans('dashboard.words.type.type_2'),
                        trans('dashboard.words.type.type_3') => trans('dashboard.words.type.type_3'),trans('dashboard.words.type.type_4') => trans('dashboard.words.type.type_4'),trans('dashboard.words.type.type_5') => trans('dashboard.words.type.type_5'),
                        '默认' => trans('dashboard.words.type.type_0'),],
                        null,
                        ['class' => 'form-control', 'id' => 'word-type', 'placeholder' => isset($word) ? $word-> type: '—选择类别—'])!!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.content') }}</label>
                        {!! Form::text('word[find]', isset($word) ? $word->find : null, ['class' => 'form-control', 'id' => 'word-find', 'placeholder' => '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.action.title') }}</label>
                        {!!  Form::select('word[replacement]', ['审核关键词' => trans('dashboard.words.action.type_1'),'禁止关键词' => trans('dashboard.words.action.type_2'),
                             '替换关键词' =>trans('dashboard.words.action.type_3')], null,
                             ['class' => 'form-control', 'id' => 'word-replacement', 'placeholder' => isset($word) ? $word-> replacement: '—过滤状态—'])!!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.substitute') }}</label>
                        {!! Form::text('word[substitute]', isset($word) ? $word->substitute : null, ['class' => 'form-control', 'id' => 'word-substitute', 'placeholder' => '']) !!}
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.word.index') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop