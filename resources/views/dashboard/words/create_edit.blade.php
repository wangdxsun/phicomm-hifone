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
                    {!! Form::hidden('word[id]', $word->id, ['class' => 'form-control', 'id' => 'word-id']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.word.store','id' => 'word-create-form', 'method' => 'post']) !!}
                @endif

                <fieldset>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.type.title') }}</label>
                        {!!  Form::select('word[type]', ['政治' => '政治', '广告' => '广告',
                        '涉枪涉爆' => '涉枪涉爆', '网络招嫖' => '网络招嫖', '淫秽信息' => '淫秽信息',
                        '默认' => '默认'], isset($word) ? $word->type : null, ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.content') }}</label>
                        {!! Form::text('word[word]', isset($word) ? $word->word : null, ['class' => 'form-control', 'required']) !!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.action.title') }}</label>
                        {!!  Form::select('word[status]', ['审核敏感词' => '审核敏感词','禁止敏感词' => '禁止敏感词',
                             '替换敏感词' => '替换敏感词'], isset($word) ? $word->status : null,
                             ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        <label>{{ trans('dashboard.words.replacement') }}</label>
                        {!! Form::text('word[replacement]', isset($word) ? $word->replacement : null, ['class' => 'form-control']) !!}
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ '保存' }}</button>
                            <a class="btn btn-default" href="{{ route('dashboard.word.index') }}">{{ '取消' }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop