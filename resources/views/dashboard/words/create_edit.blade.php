@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header" id="nodes">
        <span class="uppercase">
              {{ trans(isset($word) ? 'dashboard.words.edit.title' : 'dashboard.words.add.title') }}
        </span>
        <span class="btn btn-sm btn-success pull-right" href="{{ route('dashboard.word.create') }}">
            {{trans('dashboard.words.add.batch_in')}}
        </span>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($word))
                    {!! Form::model($word, ['route' => ['dashboard.word.update', $notice->id], 'id' => 'word-create-form', 'method' => 'patch']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.word.store','id' => 'word-create-form', 'method' => 'post']) !!}
                @endif
                @include('partials.errors')
                <fieldset>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.type.title') }}</label>
                        {!!  Form::select('word[type]', ['1' => trans('dashboard.words.type.type_1'),'2' => trans('dashboard.words.type.type_2')],null,
                        ['class' => 'form-control', 'id' => 'word-type', 'placeholder' => isset($word) ? $word-> type: '——请选择类别——'])!!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.content') }}</label>
                        {!! Form::text('word[word]', isset($word) ? $word->word : null, ['class' => 'form-control', 'id' => 'word-word', 'placeholder' => '']) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.action.title') }}</label>
                        {!!  Form::select('word[action]', ['1' => trans('dashboard.words.action.type_1'),'2' => trans('dashboard.words.action.type_2'),
                             '3' =>trans('dashboard.words.action.type_1')], null,
                             ['class' => 'form-control', 'id' => 'word-action', 'placeholder' => isset($word) ? $word-> action: '——请选择过滤方式——'])!!}
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