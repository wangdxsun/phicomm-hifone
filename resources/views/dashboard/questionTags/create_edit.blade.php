@extends('layouts.dashboard')

@section('content')
    @if(isset($sub_menu))
        @include('dashboard.partials.sub-sidebar')
    @endif
    <div class="content-wrapper">
        <div class="header sub-header">
            {{ isset($tag) ? '编辑子类' : '新增子类' }}
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if(isset($tag))
                    {!! Form::model($tag, ['route' => ['dashboard.question.tag.update', $tag->id], 'method' => 'patch', 'class' => 'create_form']) !!}
                @else
                    {!! Form::open(['route' => 'dashboard.question.tag.store', 'method' => 'post', 'class' => 'create_form']) !!}
                @endif

                <fieldset>
                    @if($tagTypes->count() > 0)
                        <div class="form-group">
                            <label>{{ '问题分类' }}</label>
                            <select name="tag[tag_type_id]" class="form-control">
                                @foreach($tagTypes as $tagType)
                                    <option value="{{ $tagType->id }}" {{ option_is_selected([$tagType, 'type', isset($tag) ? $tag : null]) }}>{{ $tagType->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label>{{ '子类名称' }}</label>
                        {!! Form::text('tag[name]', isset($tag) ? $tag->name : null, ['class' => 'form-control', 'required']) !!}
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-success">{{ trans('forms.save') }}</button>
                            <a class="btn btn-default" href="{{ back_url('dashboard.question.tag') }}">{{ trans('forms.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>


    </div>
@stop